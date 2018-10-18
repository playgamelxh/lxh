#coding=utf-8

from django.shortcuts import render

def index(request):
	content = {}
	content['hello_index'] = 'Hello world, Index'
	return render(request, 'index.html', content)
