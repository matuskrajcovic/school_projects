from django.http import HttpResponse, JsonResponse
from django.db import connection
from datetime import datetime
from django.views.decorators.csrf import csrf_exempt
import json



@csrf_exempt
def zad_2(request, id=-1):
    if request.method == 'GET':
        return zad_2_get(request)
    elif request.method == 'POST':
        return zad_2_post(request)
    elif request.method == 'DELETE':
        return zad_2_delete(request, id)
    else:
        return HttpResponse(status=404)



# returning JSON response according to GET query
def zad_2_get(request):
    q_select = q_filter = q_order = q_limit = ""
    p_select = p_filter = p_limit = []
    data = request.GET.dict()
    columns = ['id', 'br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']
    
    
    # subquery for data selection
    q_select = "SELECT {0} FROM ov.or_podanie_issues".format(', '.join(column for column in columns))
    
    
    # subquery for data filtering
    if 'query' in data:
        q_filter = " WHERE (corporate_body_name LIKE %s OR city LIKE %s"
        p_filter = ["%"+data['query']+"%", "%"+data['query']+"%"]
        if is_int(data['query']):
            q_filter += " OR cin = %s)"  
            p_filter.append(int(data['query']))
        else:
            q_filter += ")"
    
    
    # data filtering according to registration dates
    if 'registration_date_gte' in data and is_date(data['registration_date_gte']):
        if 'query' not in data:
            q_filter += " WHERE"
        else:
            q_filter += " AND"
        q_filter += " registration_date >= %s"
        p_filter.append(datetime.fromisoformat(data['registration_date_gte']).date())
    
    if 'registration_date_lte' in data and is_date(data['registration_date_lte']):
        if 'query' in data or ('registration_date_gte' in data and is_date(data['registration_date_gte'])):
            q_filter += " AND"
        else:
            q_filter += " WHERE"
        q_filter += " registration_date <= %s"
        p_filter.append(datetime.fromisoformat(data['registration_date_lte']).date())
    
    
    # subquery for data ordering
    if 'order_by' in data and data['order_by'] in columns:
        q_order = " ORDER BY " + data['order_by']
        if 'order_type' in data and data['order_type'] in ['asc', 'desc']:
            q_order += " " + data['order_type'] + " NULLS LAST, id DESC"
        else:
            q_order += " DESC NULLS LAST, id DESC"
    elif 'order_type' in data and data['order_type'] in ['asc', 'desc']:
        q_order = " ORDER BY id " + data['order_type']    
    else:
        q_order = " ORDER BY id DESC"


    # subquery for data limiting, defaults: first page, 10 results per page
    if 'page' in data and is_int(data['page']) and int(data['page']) >= 1:
        page = int(data['page']) 
    else:
        page = 1
    if 'per_page' in data and is_int(data['per_page']) and int(data['per_page']) >= 1:
        per_page = int(data['per_page'])
    else:
        per_page = 10
    q_limit = " LIMIT %s OFFSET %s;"
    p_limit = [per_page, (page-1)*per_page]

    
    # requesting the data from the database
    with connection.cursor() as cursor:
        cursor.execute(q_select + q_filter + q_order + q_limit, p_filter + p_limit)
        columns = [col[0] for col in cursor.description]
        output = [dict(zip(columns, row)) for row in cursor.fetchall()]
        cursor.execute("SELECT COUNT(id) FROM ov.or_podanie_issues" + q_filter + ";", p_filter)
        total = cursor.fetchone()[0]

    metadata = { "page": page, "per_page": per_page, "pages": (total-1)//per_page + 1, "total": total }
    return JsonResponse({"items": output, "metadata": metadata})



# inserts new data into database and returns JsonResponse with the data or errors
def zad_2_post(request):
    if request.content_type == 'application/json':
        data = json.loads(request.body)
    else:
        data = request.POST.dict()

    columns = ['br_court_name', 'kind_name', 'cin', 'registration_date', 'corporate_body_name', 'br_section', 'br_insertion', 'text', 'street', 'postal_code', 'city']
    errors = []
    now = datetime.now()
    bulletin_id = raw_id = id = -1
    
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
        return JsonResponse({'errors': errors}, status=422)

    # build all queries
    bull_query = "INSERT INTO ov.bulletin_issues (year, number, published_at, created_at, updated_at) VALUES ('{year}', (SELECT MAX(number) FROM ov.bulletin_issues WHERE year = '{year}') + 1, '{now}', '{now}', '{now}') RETURNING id;".format(year=now.year, now=now)
    
    raw_query = "INSERT INTO ov.raw_issues (bulletin_issue_id, file_name, content, created_at, updated_at) VALUES (%s, '-', '-', '{now}', '{now}') RETURNING id;".format(now=now)
        
    main_query = "INSERT INTO ov.or_podanie_issues ({cols}, created_at, updated_at, address_line, bulletin_issue_id, raw_issue_id, br_mark, br_court_code, kind_code) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '{now}', '{now}', %s, %s, %s, '-', '-', '-') RETURNING id;".format(cols=', '.join(column for column in columns), now=now);
    
    # execute queries with given parameters
    with connection.cursor() as cursor:
       cursor.execute(bull_query)
       bulletin_id = int(cursor.fetchone()[0])
       
       cursor.execute(raw_query, [bulletin_id])
       raw_id = int(cursor.fetchone()[0])
       
       address = data['street'] + ', ' + data['postal_code'] + ' ' + data['city']
       cursor.execute(main_query, [data[column] for column in columns] + [address, bulletin_id, raw_id])
       id = int(cursor.fetchone()[0])
    
    # zip the output and return it
    output = dict(zip(['id']+[column for column in columns], [id]+[data[column] for column in columns]))
    output['cin'] = int(output['cin'])
    return JsonResponse({'response': output}, status=201)



# removes data from the database, returns error on failure
def zad_2_delete(request, id):
    if is_int(id) and int(id) > -1:
    
        # if ID is in range, exesute queries
        with connection.cursor() as cursor:
            cursor.execute("SELECT bulletin_issue_id, raw_issue_id FROM ov.or_podanie_issues WHERE id = %s;", [int(id)])
            if cursor.rowcount == 1:
                ids = cursor.fetchone()
                
                # removing item from podanie_issues
                cursor.execute("DELETE FROM ov.or_podanie_issues WHERE id = %s;", [int(id)])
                cursor.execute("SELECT COUNT(*) FROM ov.or_podanie_issues WHERE raw_issue_id = %s;", [ int(ids[1])])
                
                # if there are no other podanie_issues referencing the raw issue, delete it
                if cursor.fetchone()[0] == 0:
                    cursor.execute("DELETE FROM ov.raw_issues WHERE id = %s;", [int(ids[1])])
                cursor.execute("SELECT COUNT(*) FROM ov.or_podanie_issues WHERE bulletin_issue_id = %s;", [int(ids[0])])
                
                # if there are no other podanie_issues referencing bulletin issue
                # and no other raw issues referencing it, delete it
                if cursor.fetchone()[0] == 0:
                    cursor.execute("SELECT COUNT(*) FROM ov.raw_issues WHERE bulletin_issue_id = %s;", [int(ids[0])])
                    if cursor.fetchone()[0] == 0:
                        cursor.execute("DELETE FROM ov.bulletin_issues WHERE id = %s;", [int(ids[0])])
                return HttpResponse(status=204)
                
    return JsonResponse({'error': {'message' : 'Záznam neexistuje'}}, status=404)



def is_int(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False

def is_date(date):
    try:
        #datetime.strptime(date, '%Y-%m-%d %H:%M:%S.%f')
        datetime.fromisoformat(date)
        return True
    except ValueError:
        return False
        
def to_date(date):
    #return datetime.strptime(date, '%Y-%m-%d %H:%M:%S.%f')
    return datetime.fromisoformat(date)

def new_error(field, reasons):
    return {"field": field, "reasons": reasons}