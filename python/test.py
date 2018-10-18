
#!/usr/bin/python
#coding=utf-8
import os
import unittest

from selenium import webdriver
# browser = webdriver.Chrome()
# browser.get("http://www.baidu.com")
# browser.quit()

print("Hello world!\r\n");

# name = raw_input("Input :");
# print "Name:", name;
# print r"\r\t\\\r";
# print 'Hello World! %s' %(name);

print u'中文'

arr = ['1','2','3','4']
for a in arr:
	print a
print arr



def test(x):
	print 'Function:',x
test('123')

def add_end(L=[]):
	L.append('1')
	print L
add_end();
add_end();

def add_end_new(L=None):
	if L is None:
		L = []
	L.append('1')
	print L

add_end_new();
add_end_new();

def fact(n):
	if n==1:
		return 1
	else:
		return n+fact(n-1)

n = fact(4)
print n

def fact_a(n):
	return fact_b(n, 1)
def fact_b(n, num):
	if n==1:
		return num
	else:
		return fact_b(n-1, n+num)
m = fact_a(4)
print m

arr = [d for d in os.listdir('.')]
print arr

L = ['abC', 'bcD', 123, '12A']
M = [s.lower() for s in L if isinstance(s, str)]
print L,M
for s in L:
	if isinstance(s, str):
		s.lower()
print L

def add(x, y):
	return x+y
A = reduce(add, [1,2,3,4])
print A

print "测试"

a = {'cod':1, 'data':'中文'}
class TestStringMethods(unittest.TestCase):

    def test_upper(self):
        self.assertEqual('中文', a['data'])

if __name__ == '__main__':
    unittest.main()

# TestStringMethods.test_upper('test', 'test')
