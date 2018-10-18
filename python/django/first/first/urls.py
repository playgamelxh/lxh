"""first URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/1.11/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  url(r'^$', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  url(r'^$', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.conf.urls import url, include
    2. Add a URL to urlpatterns:  url(r'^blog/', include('blog.urls'))
"""
from django.conf.urls import *
from django.contrib import admin
from . import view,testdb,search,search2

urlpatterns = [
	url(r'^$', view.hello),
    url(r'^admin/', admin.site.urls),
	url(r'^hello$', view.hello),
	url(r'^testList$', testdb.testList),
	url(r'^testAdd$', testdb.testAdd),
	url(r'^testUpdate$', testdb.testUpdate),
	url(r'^testDelete$', testdb.testDelete),
	url(r'^search-form$', search.search_form),
    url(r'^search$', search.search),
	url(r'^search-post$', search2.search_post),
]
