# -*- coding: utf-8 -*-
# !/usr/bin/python


import re
import poplib
import pprint
import email
import sys
import time
import time
import smtplib
import imaplib
from datetime import datetime, timedelta, date
from email.mime.text import MIMEText
from email.header import Header
from email.parser import Parser
from email.utils import parseaddr


# 第三方 SMTP 服务  注意这是企业邮箱  如果是个人邮箱，密码要用授权码，服务器地址没有exmail
# mail_smtp_host = "smtp.qq.com"       # 设置smtp服务器
# mail_pop_host = "pop.qq.com"         # 设置pop服务器
# mail_user = "330318747@qq.com"       # 用户名
# mail_pass = "xkhwxtetrzexbhfi"       # 口令,QQ邮箱是输入授权码，在qq邮箱设置 里用验证过的手机发送短信获得，不含空格

mail_smtp_host = "smtp.126.com"       # 设置smtp服务器
mail_pop_host = "pop.126.com"         # 设置pop服务器
mail_user = "playgamelxh@126.com"       # 用户名
mail_pass = "xkhwxtetrzexbhf1"       # 口令,QQ邮箱是输入授权码，在qq邮箱设置 里用验证过的手机发送短信获得，不含空格

'''
/*DISCRIPTION
 *	Decoding charsert
 * ARGUMENTS
 * 	string need be Decodinged
 * RETURN
 * NOTES
 */
'''


def Decoding(str):
    # new = list()
    # for x in str[0][0]:
        # print(type(x))
        # if type(x) == :
        #     new.append(x.decode('utf-8'))
        # else:
        #     new.append(x)
    # return new
        return str[0][0].decode(str[0][1])
    # if (str[0][1] is None):
    #     return unicode(str[0][0], 'gb18030')
    # else:
    #     return unicode(str[0][0], str[0][1])


'''
/*DISCRIPTION
 *	Send the mail to the unsubmit
 * ARGUMENTS
 * 	string need be Decodinged
 * RETURN
 * NOTES
 */
'''


def SendEmail():
    sender = ''
    receivers = ['']  # 接收邮件，可设置为你的QQ邮箱或者其他邮箱

    message = MIMEText('a test for python', 'plain', 'utf-8')
    message['From'] = Header("ppyy", 'utf-8')
    message['To'] = Header("you", 'utf-8')

    subject = 'my test'
    message['Subject'] = Header(subject, 'utf-8')
    try:
        smtpObj = smtplib.SMTP_SSL(mail_smtp_host, 465)
        smtpObj.login(mail_user, mail_pass)
        smtpObj.sendmail(sender, receivers, message.as_string())
        smtpObj.quit()
        print(u"邮件发送成功")
    except smtplib.SMTPException as e:
        print(e)


def GetEmail():
    try:
        pp = poplib.POP3_SSL(mail_pop_host)
        pp.user(mail_user)
        pp.pass_(mail_pass)
        ret = pp.stat()
        print(u"登录成功")
    except:
        print("can't connect to mailserver")

    # 遍历邮件的标题
    emailMsgNum, emailSize = pp.stat()
    for i in range(emailMsgNum, 1, -1):
        ret = pp.retr(i)
        # 将byte列表转为 str列表
        new = list()
        for x in ret[1]:
            new.append(x.decode('utf-8'))
        mail = email.message_from_string("\n".join(new))
        subject = email.header.decode_header(mail['subject'])
        # print(subject)
        MailSubject = Decoding(subject)
        # print(MailSubject)
        # return
        # print(MailSubject)
        if re.search(u'信用卡', MailSubject):
            resp, lines, octets = pp.retr(i)

            msg_content = b'\r\n'.join(lines).decode('utf-8')
            msg = Parser().parsestr(msg_content)
            # print msg
            print_info(msg)
            break
    pp.quit()


def guess_charset(msg):
    charset = msg.get_charset()
    if charset is None:
        content_type = msg.get('Content-Type', '').lower()
        pos = content_type.find('charset=')
    if pos >= 0:
        charset = content_type[pos + 8:].strip()
    return charset


def decode_str(s):
    value, charset = email.header.decode_header(s)[0]
    if charset:
        value = value.decode(charset)
    return value


def print_info(msg, indent=0):
    if indent == 0:
        for header in ['From', 'To', 'Subject']:
            value = msg.get(header, '')
            if value:
                if header == 'Subject':
                    value = decode_str(value)
                else:
                    hdr, addr = parseaddr(value)
                    name = decode_str(hdr)
                    value = u'%s <%s>' % (name, addr)
            print('%s%s: %s' % (' ' * indent, header, value))
    if (msg.is_multipart()):
        parts = msg.get_payload()
        for n, part in enumerate(parts):
            print('%spart %s' % (' ' * indent, n))
            print('%s--------------------' % (' ' * indent))
            print_info(part, indent + 1)
    else:
        content_type = msg.get_content_type()
        if content_type == 'text/plain' or content_type == 'text/html':
            content = msg.get_payload(decode=True)
            charset = guess_charset(msg)
            if charset:
                content = content.decode(charset)
            print('%sText: %s' % (' ' * indent, content + '...'))
        else:
            print('%sAttachment: %s' % (' ' * indent, content_type))


GetEmail()
