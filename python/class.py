#!/usr/bin/python
#coding=utf-8
class a(object):
	def __init__(self, name, age, sex):
		self.name = name
		self.age  = age
		self.__sex = sex
	def p(obj):
		print "name:%s,age:%d,sex:%s"%(obj.name, obj.age, obj.__sex)

abc = a('张三', 15, '男')
print abc.name,abc.age
abc.p()
