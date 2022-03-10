from django.urls import path
from . import views

urlpatterns = [
    path('v1/health/', views.zad_1),
    path('v1/ov/submissions/', views.zad_2),
    path('v1/ov/submissions', views.zad_2),
    path('v1/ov/submissions/<str:id>', views.zad_2),
    path('v1/ov/submissions/<str:id>/', views.zad_2),
    path('v1/companies/', views.zad_3),
    path('v2/ov/submissions/', views.zad5_2),
    path('v2/ov/submissions', views.zad5_2),
    path('v2/ov/submissions/<str:id>', views.zad5_2),
    path('v2/ov/submissions/<str:id>/', views.zad5_2),
    path('v2/companies/', views.zad5_3)
]
