from django.http import HttpResponse, JsonResponse
from django.db import connection
from .zad2 import is_int, is_date, to_date
from datetime import datetime

def zad_3(request):
    
    if request.method == 'GET':

        q_select = q_filter = q_order = q_limit = ""
        p_select = p_filter = p_limit = []
        data = request.GET.dict()
        columns = ['cin', 'name', 'br_section', 'address_line', 'last_update', 'or_podanie_issues_count', 'znizenie_imania_issues_count' , 'likvidator_issues_count', 'konkurz_vyrovnanie_issues_count', 'konkurz_restrukturalizacia_actors_count']
        tables = ["or_podanie_issues", "likvidator_issues", "konkurz_vyrovnanie_issues", "znizenie_imania_issues", "konkurz_restrukturalizacia_actors"]
        
        
        #subquery for data selection
        # we join all the tables together, 
        # and during each join we count number of occurences of company_id in current table
        q_select += "SELECT {0} ".format(', '.join(column for column in columns))
        q_select += "FROM ov.companies AS c "
        for table in tables:
            q_select += "LEFT JOIN (SELECT DISTINCT company_id, COUNT(*) OVER (PARTITION BY company_id) AS {0} FROM ov.{1}) AS t{2} ON t{2}.company_id=c.cin ".format(table+'_count', table, tables.index(table))
        
        
        # subquery for data filtering
        if 'query' in data:
            q_filter = " WHERE (name LIKE %s OR address_line LIKE %s)"
            p_filter = ["%"+data['query']+"%", "%"+data['query']+"%"]
        
        
        # data filtering according to registration dates
        if 'last_update_gte' in data and is_date(data['last_update_gte']):
            if 'query' not in data:
                q_filter += " WHERE"
            else:
                q_filter += " AND"
            q_filter += " last_update >= %s"
            p_filter.append(datetime.fromisoformat(data['last_update_gte']).date())
    
        if 'last_update_lte' in data and is_date(data['last_update_lte']):
            if 'query' in data or ('last_update_gte' in data and is_date(data['last_update_gte'])):
                q_filter += " AND"
            else:
                q_filter += " WHERE"
            q_filter += " last_update <= %s"
            p_filter.append(datetime.fromisoformat(data['last_update_lte']).date())
        
        
        # subquery for data ordering, default is cin DESC, which is also the secondary sort
        if 'order_by' in data and data['order_by'] in columns:
            q_order = " ORDER BY " + data['order_by']
            if 'order_type' in data and data['order_type'] in ['asc', 'desc']:
                q_order += " " + data['order_type'] + " NULLS LAST, cin DESC"
            else:
                q_order += " DESC NULLS LAST, cin DESC"
        
        elif 'order_type' in data and data['order_type'] in ['asc', 'desc']:
            q_order = " ORDER BY cin " + data['order_type']
        
        else:
            q_order = " ORDER BY cin DESC"
        
        
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
        
        
        # send the query to the database and receive output + metadata query
        with connection.cursor() as cursor:
            
            cursor.execute(q_select + q_filter + q_order + q_limit, p_filter + p_limit)
            columns = [col[0] for col in cursor.description]
            output = [dict(zip(columns, row)) for row in cursor.fetchall()]

            cursor.execute("SELECT COUNT(cin) FROM ov.companies" + q_filter + ";", p_filter)
            total = cursor.fetchone()[0]
        
        metadata = { "page": page, "per_page": per_page, "pages": (total-1)//per_page + 1, "total": total }
        return JsonResponse({"items": output, "metadata": metadata})

    return HttpResponse(status=404)