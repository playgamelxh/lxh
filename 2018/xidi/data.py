# 登录数据中心，获取分销当天销量前10的商品
import requests

url = "http://internal.passport.xidibuy.com/passport/sign"
headers = {
    'user-agent': "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
    'Referer': "http://internal.passport.xidibuy.com/passport/login?app=newcmdata",
}
post_data = {'username': "lvxiaohu", "password": "330318747"}
parameters = {}
# 提交get请求
# P_get = request.get(url, params=parameters)
# 提交post请求
P_post = requests.post(url, headers=headers, data=post_data)
print(P_post)

# 请求数据
url = "http://data.xiditech.com/goodsale/getpagecontent/?timeBegin=2018-9-29&timeEnd=2018-9-29&agentor=&agentId=0&countryid=&shopName=&cate1=&cate2=&cate3=&depart=0&brandName=&prodSn=&skuId=&inside=1&order=5&page=1"
P_get = requests.get(url)
print(P_get)
