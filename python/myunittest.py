#!/usr/bin/python
#encoding=utf-8
#导入测试模块

import unittest

class myclass:
	def __init__(self):
		pass

	def sum(self, x, y):
		return x+y

	def sub(self, x, y):
		return x-y

	def cheng(self, x, y):
		return x*y

class mytest(unittest.TestCase):

	# def __init__(self, *args, **kwargs):
	# 	unittest.TestCase.__init__(self, *args, **kwargs)

	#初始化工作
	def setUp(self):
		self.tclass = myclass.myclass()		#实例化了被测试模块中的类
	#退出清理工作
	def tearDown(self):
		pass

	#具体的测试用例
	def test_sum(self):
		self.assertEqual(self.tclass.sum(1,2) ,3)

	def test_sub(self):
		self.assertEqual(self.tclass.sub(4,2) ,2)

	def test_cheng(self):
		self.assertEqual(self.tclass.cheng(2,3) ,5)

if __name__ == '__main__':

	suite = unittest.TestSuite()
	suite.addTest(mytest("test_sum"))
	suite.addTest(mytest("test_sub"))

	runner = unittest.TextTestRunner()
	runner.run(suite)
