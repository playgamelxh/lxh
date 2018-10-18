# -*- coding: utf-8 -*-
#coding=utf-8

import time
from bitcoinrpc.authproxy import AuthServiceProxy, JSONRPCException


class Chain(object):

	def __init__(self, user, passwd, ip, port):
		self.connection = AuthServiceProxy("http://%s:%s@%s:%s"%(user, passwd, ip, port))

	#获取最新块
	def getbestblockhash(self):
		return self.connection.getbestblockhash()

	#获取指定高度的hash
	def getblockhash(self,height):
		return self.connection.getblockhash(height)

	#根据hash获取基本信息
	def getblock(self, hash):
		return self.connection.getblock(hash)

	#
	def listsinceblock(self, hash, num):
		return self.connection.listsinceblock(hash,num)

	def gettransaction(self, txid):
		return self.connection.gettransaction(txid)

	def getrawtransaction(self, txid, verbose=0):
		return self.connection.getrawtransaction(txid, verbose)

if __name__ == "__main__":
	rpc_user = "bihu"
	rpc_password = "bihuo123456"
	rpc_ip = "39.107.112.5"
	rpc_port = 30000
	chain = Chain(user=rpc_user, passwd=rpc_password, ip=rpc_ip, port=rpc_port)

	# best_block_hash = chain.getbestblockhash()
	# print "最新块Hash:",best_block_hash
	# hash_info = chain.getblock(hash = best_block_hash)
	# print "最新块概况：",hash_info
	# print "高度：",hash_info['height']
	# print "txid:",hash_info['merkleroot']
	# print "前一个hash：",hash_info['previousblockhash']
	# print "难度：",hash_info['difficulty']
	# print "确认数目：",hash_info['confirmations']
	# print "时间：", time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(hash_info['time']))
	print "==================================================="

	height = 266389
	hash = chain.getblockhash(height)
	print "块%sHash:"%(height),hash
	hash_info = chain.getblock(hash = hash)
	print "块%s概况："%(height),hash_info
	print "块%s高度："%(height),hash_info['height']
	print "块%stxid:"%(height),hash_info['merkleroot']
	print "块%s前一个hash："%(height),hash_info['previousblockhash']
	print "块%s难度："%(height),hash_info['difficulty']
	print "块%s确认数目："%(height),hash_info['confirmations']
	print "块%s时间："%(height), time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(hash_info['time']))

	for txid in hash_info['tx']:
		temp = chain.getrawtransaction(txid=txid)
		print "根据txid获取区块加密信息：",temp
		temp = chain.getrawtransaction(txid=txid, verbose=1)
		print "根据txid获取区块解密信息：",temp


	# since_block = chain.listsinceblock(hash, 10)
	# print since_block
