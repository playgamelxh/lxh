#!/usr/bin/python
#coding=utf-8
#https://blog.csdn.net/u013695144/article/details/37498785  接口详情参考

from bitcoinrpc.authproxy import AuthServiceProxy, JSONRPCException
rpc_user = "bihu"
rpc_password = "bihuo123456"

# rpc_user and rpc_password are set in the bitcoin.conf file
rpc_connection = AuthServiceProxy("http://%s:%s@39.107.112.5:30000"%(rpc_user, rpc_password))
#概况
best_block_hash = rpc_connection.getbestblockhash()
print "概况：",rpc_connection.getblock(best_block_hash)
#总区块数
getblockcount = rpc_connection.getblockcount();
print "区块总数：",getblockcount

getconnectioncount = rpc_connection.getconnectioncount();
print "节点连接总数：",getconnectioncount

getdifficulty = rpc_connection.getdifficulty();
print "难度系数：",getdifficulty

getgenerate = rpc_connection.getgenerate();
print "是否在生成hash：",getgenerate

gethashespersec = rpc_connection.gethashespersec();
print "返回一个新的哈希值每秒的性能测量而产生：",gethashespersec

getinfo = rpc_connection.getinfo();
print "返回一个包含各种状态信息的对象：",getinfo

getblockhash = rpc_connection.getblockhash(1);
print "区块ID232062的hash：",getblockhash

getblock = rpc_connection.getblock(getblockhash);
print "返回一个hash交易信息：",getblock
# arr = json.loads(getblock, list)
print getblock['merkleroot']
print getblock['tx']
print getblock['tx'][0]

listsinceblock = rpc_connection.listsinceblock(getblockhash);
print "从块[blockhash]（不含）开始获取块中的所有事务，或者省略所有事务。一次最多25个：",listsinceblock

#
gettransaction = rpc_connection.gettransaction(listsinceblock['transactions'][0]['txid']);
print "返回一个hash交易信息：",gettransaction

listaccounts = rpc_connection.listaccounts();
print "返回账户信息对象，键为账户名，值为余额：",listaccounts
for a in listaccounts:
	print a

masternodelist = rpc_connection.masternodelist();
print "节点列表："
i = 0
for k,v in masternodelist.items():
	i += 1
	print k
print "总节点数：",i

getblockchaininfo = rpc_connection.getblockchaininfo();
print "返回账户信息对象，键为账户名，值为余额：",getblockchaininfo

#获取前100个链信息
commands = [ [ "getblockhash", height] for height in range(100) ]
block_hashes = rpc_connection.batch_(commands)
blocks = rpc_connection.batch_([ [ "getblock", h ] for h in block_hashes ])
block_times = [ block["time"] for block in blocks ]
# print(block_times)
# print blocks
