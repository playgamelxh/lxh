#!/usr/bin/python
#coding=utf-8
#https://blog.csdn.net/u013695144/article/details/37498785  接口详情参考

from bitcoinrpc.authproxy import AuthServiceProxy, JSONRPCException
rpc_user = "bihu"
rpc_password = "bihuo123456"

# rpc_user and rpc_password are set in the bitcoin.conf file
rpc_connection = AuthServiceProxy("http://%s:%s@39.107.112.5:30000"%(rpc_user, rpc_password))

#总区块数
getblockcount = rpc_connection.getblockcount();
print "区块总数：",getblockcount

for n in range(234423, 234424):
	getblockhash = rpc_connection.getblockhash(n);
	print "区块ID：%d的hash："%(n),getblockhash
	getblock = rpc_connection.getblock(getblockhash);
	print "返回有关给定块散列的信息：",getblock
	print getblock['tx'][0]
	# t = getblock['tx']
	# for k,v in getblock['tx'].items():
	# 	print k
	# listsinceblock = rpc_connection.listsinceblock();
	# print "从块[blockhash]（不含）开始获取块中的所有事务，或者省略所有事务。一次最多25个：",listsinceblock
	# gettransaction = rpc_connection.gettransaction(listsinceblock['transactions'][0]['txid']);
	# gettransaction = rpc_connection.gettransaction(getblock['tx'][0]);

	# gettransaction = rpc_connection.gettransaction('025dac58395bd3c5a003fac763d9e470f0a95e4d4b4eb04a533359aa4e7ce5aa');
	# print "返回关于给定事务散列的对象：",gettransaction
	getrawtransaction = rpc_connection.getrawtransaction(getblock['tx'][1]);
	print getrawtransaction
	decoderawtransaction = rpc_connection.decoderawtransaction(getrawtransaction)
	print decoderawtransaction
	print decoderawtransaction['vout'][0]['scriptPubKey']['addresses'][0]
	# getaccountaddress = rpc_connection.getaccountaddress(decoderawtransaction['vout'][0]['scriptPubKey']['addresses'][0])
	# print getaccountaddress
	getbalance = rpc_connection.getbalance(decoderawtransaction['vout'][0]['scriptPubKey']['addresses'][0])
	print getbalance
	# getrawtransaction = rpc_connection.getrawtransaction(decoderawtransaction['vin'][7]['txid']);
	# print getrawtransaction
	# decoderawtransaction = rpc_connection.decoderawtransaction(getrawtransaction)
	# print decoderawtransaction
