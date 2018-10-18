#coding=utf-8
# from django.http import HttpResponse
#
# def hello(request):
#     return HttpResponse("区块链浏览器")

from django.shortcuts import render

def hello(request):
	content = {}
	content['hello'] = 'Hello world'
	return render(request, 'hello.html', content)
