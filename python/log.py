from bitcoinrpc.authproxy import AuthServiceProxy, JSONRPCException
import logging

rpc_user = "bihu"
rpc_password = "bihuo123456"

logging.basicConfig()
logging.getLogger("BitcoinRPC").setLevel(logging.DEBUG)

rpc_connection = AuthServiceProxy("http://%s:%s@39.107.112.5:30000"%(rpc_user, rpc_password))
print(rpc_connection.getinfo())
