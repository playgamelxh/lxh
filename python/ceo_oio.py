#!/usr/bin/python
#coding=utf-8
import os,time
import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
import urllib,urllib2,random

class CEO(unittest.TestCase):

	def setUp(self):
		self.driver = webdriver.Chrome()
		self.baseurl="https://ceo.bi/oioc.jsp"
		self.driver.get(self.baseurl)
		self.driver.set_window_size(1920, 1080)
		self.buy_price = 0.00082
		self.sell_price = 0.00085
		self.num = 50000
		self.sleep = 30

	def tearDown(self):
		# pass
		self.driver.quit()

	def login(self):
		#执行js
		js="loginpop();"
		self.driver.execute_script(js)
		#等待登录弹窗
		WebDriverWait(self.driver, 20, 1).until(EC.presence_of_element_located((By.ID,'login_moble')))
		time.sleep(1)
		self.driver.find_element_by_id('login_moble').send_keys("13803895149")
		self.driver.find_element_by_id('login_password').send_keys("btc330318747")
		self.cookie = ""
		for cookie in self.driver.get_cookies():
			self.cookie += "%s=%s;"%(cookie['name'], cookie['value'])

		flag = False
		while not flag:
			self.driver.find_element_by_id('login_verify').clear()
			self.driver.find_element_by_id('login_verify').send_keys(raw_input("验证码:"))
			js="footer_login();"
			self.driver.execute_script(js)
			time.sleep(3)
			flag = self.isElementExist(id='moble_verify_btn')
			print "短信验证码按钮是否存在：",flag
		#不出短信验证码的情况
		## css = self.driver.find_element_by_id("su").value_of_css_property("display")
		# self.driver.find_element_by_id('moble_verify_btn').click()
		# flag = True
		# while flag:
		# 	self.driver.find_element_by_id('moble_verifys').clear()
		# 	self.driver.find_element_by_id('moble_verifys').send_keys(raw_input("短信码:"))
		# 	js="footer_login();"
		# 	self.driver.execute_script(js)
		# 	time.sleep(4)
		# 	flag = self.isElementExist(id='moble_verify_btn')

	def isLogin(self):
		flag = self.isElementExist(id='show_mobile')
		return flag

	def isElementExist(self, id='', cl='', css=''):
		flag=True
		try:
			if not id is None:
				self.driver.find_element_by_id(id)
				return flag
			elif not cl is None:
				self.driver.find_element_by_class_name(cl)
				return flag
			elif not css is None:
				self.driver.find_element_by_css_selector(css)
				return flag
			else:
				return False
		except:
			flag=False
			return flag
	#获取交易信息
	def get_info(self):
		post_data = {'market':'oioc_cny'}
		post_data_urlencode = urllib.urlencode(post_data)
		random.seed()
		t = random.random()
		url = "https://ceo.bi/trade/index_json?t=" + str(t)
		header = { "Cookie" : self.cookie }
		#请求异常重试
		info = {}
		for i in range(20):
			try:
				# print header
				req = urllib2.Request(url = url, data = post_data_urlencode, headers = header)
				#print req
				res_data = urllib2.urlopen(req)
				#print res_data
				info = res_data.read()
			except:
				time.sleep(5)
			else:
				break
		return info

	#自动挂单
	def trade(self):
		while True:
			info = self.get_info()
			#防止异常
			try:
				info = eval(info)
				break
			except:
				continue

		print "可用资金：%s"%(info['finance'][0])
		print "冻结资金：%s"%(info['finance'][1])
		print "可用币数：%s"%(info['finance'][2])
		print "冻结币数：%s"%(info['finance'][3])
		print "总资产数：%s"%(info['finance'][4])
		print "最低卖单价：%s"%(info['depth']['s'][9][0])
		print "最高买单价：%s"%(info['depth']['b'][0][0])
		#大于三块钱开始购买
		if float(info['finance'][0]) > 5 and float(info['depth']['b'][0][0]) < self.sell_price:
			#记录最高买价
			if self.buy_price==0:
				self.buy_price = float(info['depth']['b'][0][0])
			elif float(info['depth']['b'][0][0]) > self.buy_price:
				self.buy_price = float(info['depth']['b'][0][0])

			num = float(info['finance'][0])/float(info['depth']['b'][0][0])
			print "花费%sRMB，按最高买单价格%s，买入%s个币"%(info['finance'][0], info['depth']['b'][0][0], num)
			# self.buy(price=float(info['depth']['b'][0][0]), num=num)
			self.buy(price=info['depth']['b'][0][0], num=self.num)

		#多余2w的币开始卖
		if float(info['finance'][2]) > self.num and float(info['depth']['s'][9][0]) > self.buy_price:
			#记录最低卖价
			if self.sell_price==0:
				self.sell_price = info['depth']['s'][9][0]
			elif float(info['depth']['b'][0][0]) < self.sell_price:
				self.sell_price = info['depth']['s'][9][0]

			money = float(info['finance'][2])*float(info['depth']['s'][9][0])
			print "按最低卖单价格%s，卖掉%s个币，获得%sRMB"%(info['depth']['s'][9][0], info['finance'][2], money)
			# self.sale(price=info['depth']['s'][9][0], num=float(info['finance'][2]))
			self.sale(price=info['depth']['s'][9][0], num=self.num)
	#买入
	def buy(self,price=0.00,num=0):
		self.driver.find_element_by_id('buy_price').clear()
		self.driver.find_element_by_id('buy_price').send_keys(price)
		self.driver.find_element_by_id('buy_num').clear()
		self.driver.find_element_by_id('buy_num').send_keys(num)
		self.driver.find_element_by_id('buy_paypassword').clear()
		self.driver.find_element_by_id('buy_paypassword').send_keys('1xh330318747?')
		self.driver.execute_script('tradeadd_buy();')
	#卖出
	def sale(self,price=0.00, num=0):
		self.driver.find_element_by_id('sell_price').clear()
		self.driver.find_element_by_id('sell_price').send_keys(price)
		self.driver.find_element_by_id('sell_num').clear()
		self.driver.find_element_by_id('sell_num').send_keys(num)
		self.driver.find_element_by_id('sell_paypassword').clear()
		self.driver.find_element_by_id('sell_paypassword').send_keys('1xh330318747?')
		self.driver.execute_script('tradeadd_sell();')

	def test(self):
		#判断是否登录，未登录，先登录
		flag = self.isLogin()
		if flag == False:
			self.login()
			WebDriverWait(self.driver, 20, 1).until(EC.presence_of_element_located((By.ID,'show_mobile')))

		#获取交易信息
		while True:
			print "==========交易循环开始================="
			self.trade()
			print "==========交易循环结束================="
			time.sleep(self.sleep)

if __name__ == '__main__':
	unittest.main()
