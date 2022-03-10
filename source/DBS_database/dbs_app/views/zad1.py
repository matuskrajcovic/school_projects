from django.http import JsonResponse
from django.db import connection

def zad_1(request):
    with connection.cursor() as cursor:
        cursor.execute("SELECT date_trunc('second', current_timestamp - pg_postmaster_start_time()) as uptime;")
        columns = [col[0] for col in cursor.description]
        rows = [str(row[0]).replace(',','') for row in cursor.fetchall()]
        output = dict(zip(columns, rows))
    return JsonResponse({"pgsql": output})