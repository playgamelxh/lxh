#!/usr/bin/python
#encoding=utf-8

# from django.http import HttpResponse
#
# def hello(request):
#     return HttpResponse("Hello world ! ")
#

from django.shortcuts import render

def hello(request):
	context = {}
	context['hello'] = 'Hello World!'
	context['bl'] = True
	context['title'] = 'Title'
	return render(request, 'hello.html', context)
