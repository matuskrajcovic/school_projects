from django.http import HttpResponse, JsonResponse
from .zad5_2 import is_int, is_date, to_date
from datetime import datetime
from dbs_app.models import *
from django.db.models import Count, Avg, Window, F, Q, FilteredRelation, Prefetch, Case, When, Value

def zad5_3(request):
    
    if request.method == 'GET':

        data = request.GET.dict()
        columns = ['cin', 'name', 'br_section', 'address_line', 'last_update', 'or_podanie_issues_count', 'znizenie_imania_issues_count' , 'likvidator_issues_count', 'konkurz_vyrovnanie_issues_count', 'konkurz_restrukturalizacia_actors_count']
        aggregations = ['or_podanie_issues_count', 'znizenie_imania_issues_count' , 'likvidator_issues_count', 'konkurz_vyrovnanie_issues_count', 'konkurz_restrukturalizacia_actors_count']
        tables = ["or_podanie_issues", "likvidator_issues", "konkurz_vyrovnanie_issues", "znizenie_imania_issues", "konkurz_restrukturalizacia_actors"]
        

        # main query
        set = companies.objects.annotate(
            or_podanie_issues_count = Count('or_podanie_issues', distinct=True),
            znizenie_imania_issues_count = Count('znizenie_imania_issues', distinct=True),
            likvidator_issues_count = Count('likvidator_issues', distinct=True),
            konkurz_vyrovnanie_issues_count = Count('konkurz_vyrovnanie_issues', distinct=True),
            konkurz_restrukturalizacia_actors_count = Count('konkurz_restrukturalizacia_actors', distinct=True)
        )


        # data filtering
        if 'query' in data:
            set = set.filter(
                Q(name__icontains=data['query']) 
                | Q(address_line__icontains=data['query']))
           
        
        # filtering according to date
        if 'last_update_gte' in data and is_date(data['last_update_gte']):
            set = set.filter(last_update__gte = to_date(data['last_update_gte']).date())
        
        if 'last_update_lte' in data and is_date(data['last_update_lte']):
            set = set.filter(last_update__lte = to_date(data['last_update_lte']).date())
            
        
        # data ordering, default cin DESC
        if 'order_by' in data and data['order_by'] in columns:
            if 'order_type' in data and data['order_type'] == 'asc':
                set = set.order_by(F(data['order_by']).asc(nulls_last=True))
            else:
                set = set.order_by(F(data['order_by']).desc(nulls_last=True))
        elif 'order_type' in data and data['order_type'] == 'asc':
            set = set.order_by(F('cin').asc(nulls_last=True))
        else:
            set = set.order_by(F('cin').desc(nulls_last=True))
        
    
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

    return HttpResponse(status = 404)