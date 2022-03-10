from django.urls import path, include, re_path

urlpatterns = [
    path('', include('dbs_app.urls'))
]
