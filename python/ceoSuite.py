# coding=utf-8
'''
Created on 2018-5-7
@author: lxh
Project:CEO测试
'''
import unittest
import ceo_oio

#构造测试集
suite = unittest.TestSuite()
suite.addTest(ceo_oio.CEO('test'))

if __name__=='__main__':
    #执行测试
    runner = unittest.TextTestRunner()
    runner.run(suite)
