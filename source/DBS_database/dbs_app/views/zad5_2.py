from django.http import HttpResponse, JsonResponse
from django.db import connection
from datetime import datetime
from django.utils import timezone
from django.views.decorators.csrf import csrf_exempt
import json
from dbs_app.models import *
from django.db.models import F, Q, Max
from django.forms.models import model_to_dict
import dateutil.parser



@csrf_exempt
def zad5_2(request, id=-1):
    if request.method == 'GET':
        if id == -1:
            return zad5_2_get(request)
        else:
            return zad5_2_get_one(request, id)
    elif request.method == 'POST':
        return zad5_2_post(request)
    elif request.method == 'DELETE':
        return zad5_2_delete(request, id)
    elif request.method == 'PUT':
        return zad5_2_put(request, id)
    else:
        return HttpResponse(status = 404)


# GET for multiple entries, with paging
def zad5_2_get(request):

    data = request.GET.dict()
    set = or_podanie_issues.objects

    columns = ['id', 'br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']

    # data filtering
    if 'query' in data:
        if is_int(data['query']):
            set = set.filter(
                Q(cin=int(data['query']))
                | Q(corporate_body_name__icontains=data['query']) 
                | Q(city__icontains=data['query']))
        else:
            set = set.filter(
                Q(corporate_body_name__icontains=data['query']) 
                | Q(city__icontains=data['query']))
    

    # filtering according to date
    if 'registration_date_gte' in data and is_date(data['registration_date_gte']):
        set = set.filter(registration_date__gte = to_date(data['registration_date_gte']).date())
    
    if 'registration_date_lte' in data and is_date(data['registration_date_lte']):
        set = set.filter(registration_date__lte = to_date(data['registration_date_lte']).date())
    

    # data ordering, default ID DESC
    if 'order_by' in data and data['order_by'] in columns:
        if 'order_type' in data and data['order_type'] == 'asc':
            set = set.order_by(F(data['order_by']).asc(nulls_last=True))
        else:
            set = set.order_by(F(data['order_by']).desc(nulls_last=True))
    elif 'order_type' in data and data['order_type'] == 'asc':
        set = set.order_by('id')
    else:
        set = set.order_by('-id')
    

    # data paging, defaults page 1 per_page 10
    if 'page' in data and is_int(data['page']) and int(data['page']) >= 1:
        page = int(data['page']) 
    else:
        page = 1
    if 'per_page' in data and is_int(data['per_page']) and int(data['per_page']) >= 1:
        per_page = int(data['per_page'])
    else:
        per_page = 10
    

    total = set.count()
    set = set[(page-1)*per_page:(page-1)*per_page+per_page]
    
    output = list(set.values(*columns))
    metadata = { "page": page, "per_page": per_page, "pages": (total-1)//per_page + 1, "total": total }
    
    return JsonResponse({"items": output, "metadata": metadata}, status = 200)


# POST new entry into the database
def zad5_2_post(request):
    
    # check if the json is valid
    try:
        data = json.loads(request.body)
    except:
        data = {}

    columns = ['br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']
    errors = []
    
    # check for errors in the data
    for column in columns:
        if column in data:
            if column == 'cin' and not is_int(data['cin']):
                errors.append(new_error(column, ['required', 'not_number']))
            elif column == 'registration_date':
                if not is_date(data['registration_date']):
                    errors.append(new_error(column, ['required']))
                elif to_date(data['registration_date']).year != datetime.now().year:
                    errors.append(new_error(column, ['required', 'invalid_range']))
        else:
            errors.append(new_error(column, ['required']))
    
    # returns errors if any
    if len(errors) > 0:
        return JsonResponse({'errors': errors}, status = 422)
    
    now = datetime.now(tz=timezone.utc)
    
    # create bulletin and raw issues
    bulletin = bulletin_issues(
        year = now.year,
        number = bulletin_issues.objects.filter(year=now.year).aggregate(Max('number'))['number__max'] + 1,
        published_at = now, created_at = now, updated_at = now)
    bulletin.save()

    raw = raw_issues(
        bulletin_issue_id = bulletin.id,
        file_name = '-', content = '-', created_at = now, updated_at = now)
    raw.save()

    data['registration_date'] = to_date(data['registration_date'])
    data['cin'] = int(data['cin'])
    
    # create new podanie issue
    podanie = or_podanie_issues(
        **data,
        created_at = now, updated_at = now,
        address_line = data['street'] + ', ' + data['postal_code'] + ' ' + data['city'],
        bulletin_issue_id = bulletin.id,
        raw_issue_id = raw.id,
        br_mark='', br_court_code='', kind_code='')
    podanie.save()
    
    output = model_to_dict(podanie, fields = ['id'] + columns)
    output['registration_date'] = output['registration_date'].date()
    
    return JsonResponse({'response': output}, status = 201)



# DELETE an entry from the database
def zad5_2_delete(request, id):
    if is_int(id) and int(id) > -1:
        set_podanie = or_podanie_issues.objects.filter(id=id)
        
        # if we find the entry save bulletin and raw issue numbers
        if set_podanie:
            podanie = set_podanie.first()
            raw_id = podanie.raw_issue_id
            bulletin_id = podanie.bulletin_issue_id
            
            podanie.delete()

            # remove entries from bulletin and raw issue tables
            if or_podanie_issues.objects.filter(raw_issue_id = raw_id).count() == 0:
                raw = raw_issues.objects.get(id=raw_id)
                raw.delete()
            if or_podanie_issues.objects.filter(bulletin_issue_id = bulletin_id).count() == 0 and raw_issues.objects.filter(bulletin_issue_id = bulletin_id).count() == 0:
                bulletin = bulletin_issues.objects.get(id=bulletin_id)
                bulletin.delete()
            
            return HttpResponse(status = 204)
        
    return JsonResponse({'error': {'message' : 'Záznam neexistuje'}}, status = 404)


# PUT new data into the entry
def zad5_2_put(request, id):
    
    if is_int(id) and int(id) > -1:
        item = or_podanie_issues.objects.filter(id=id)
        if not item:
            return JsonResponse({'error': {'message' : 'Záznam neexistuje'}}, status = 404)
    
        # checks if the json is valid
        try:
            data = json.loads(request.body)
        except:
            return HttpResponse(status = 422)

        columns = ['br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']
        update = {}
        errors = []

        # check for errors in the data, leave only valid entries
        for column in data:
            if column in columns:
                if column == 'cin':
                    if not is_int(data['cin']):
                        errors.append(new_error(column, ['not_number']))
                    else:
                        update[column] = data[column]
                elif column == 'registration_date':
                    if not is_date(data['registration_date']) or to_date(data['registration_date']).year != datetime.now().year:
                        errors.append(new_error(column, ['invalid_range']))
                    else:
                        update[column] = data[column]
                else:
                    if isinstance(data[column], str):
                        update[column] = data[column]
                    else:
                        errors.append(new_error(column, ['not_string']))
            else:
                pass
        
        # returns errors if any
        if len(errors) > 0:
            return JsonResponse({'errors': errors}, status = 422)

        # if there are no valid entries
        if len(update) == 0:
            return HttpResponse(status = 422)

        # update the item
        item.update(**update)
        
        output = model_to_dict(item.first(), fields = ['id'] + columns)
        
        return JsonResponse({'response': output}, status = 201)
    
    return JsonResponse({'error': {'message' : 'Záznam neexistuje'}}, status = 404)


# GET one entry
def zad5_2_get_one(request, id):
    if is_int(id) and int(id) > -1:
        columns = ['id', 'br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']
        
        item = or_podanie_issues.objects.filter(id=id).first()
        if item:
            output = model_to_dict(item, columns)
            return JsonResponse({'response': output}, status = 200)

    return JsonResponse({'error': {'message' : 'Záznam neexistuje'}}, status = 404) 


def is_int(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False

def is_date(date):
    try:
        parsed_date = dateutil.parser.parse(date)
        datetime.fromisoformat(parsed_date.isoformat())
        return True
    except ValueError:
        return False
        
def to_date(date):
    return datetime.fromisoformat(dateutil.parser.parse(date).isoformat())

def new_error(field, reasons):
    return {"field": field, "reasons": reasons}