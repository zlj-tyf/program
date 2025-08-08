import smtplib
import ssl
import sys
import json
from email.mime.text import MIMEText
from email.utils import formataddr
from user_agents import parse

# 读取配置
with open('scripts/config.json', 'r', encoding='utf-8') as f:
    config = json.load(f)

SMTP_SERVER = config["SMTP_SERVER"]
SMTP_PORT = config["SMTP_PORT"]
SENDER_EMAIL = config["SENDER_EMAIL"]
SENDER_PASSWORD = config["SENDER_PASSWORD"]
SENDER_NAME = config["SENDER_NAME"]
TARGET_EMAIL = config["TARGET_EMAIL"]
TARGET_NAME = config["TARGET_NAME"]

# 从命令行参数获取数据
student_id = sys.argv[1]
feedback_content = sys.argv[2]
user_ip = sys.argv[3]
user_agent_str = sys.argv[4]

# 解析 User-Agent
ua = parse(user_agent_str)

device_type = "电脑"
if ua.is_mobile:
    device_type = "手机"
elif ua.is_tablet:
    device_type = "平板"

os = ua.os.family + " " + ua.os.version_string
browser = ua.browser.family + " " + ua.browser.version_string

friendly_user_agent = f"{device_type} / 操作系统: {os} / 浏览器: {browser}"

# 生成 HTML 表格邮件
html_content = f"""
<html>
<body>
<h3>新的用户反馈</h3>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
    <tr><th>学号</th><td>{student_id if student_id else '（未填写）'}</td></tr>
    <tr><th>IP 地址</th><td>{user_ip}</td></tr>
    <tr><th>设备信息</th><td>{friendly_user_agent}</td></tr>
    <tr><th>反馈内容</th><td>{feedback_content}</td></tr>
</table>
</body>
</html>
"""

def send_email(subject, body):
    msg = MIMEText(body, 'html', 'utf-8')
    msg['From'] = formataddr((SENDER_NAME, SENDER_EMAIL))
    msg['To'] = formataddr((TARGET_NAME, TARGET_EMAIL))
    msg['Subject'] = subject

    context = ssl.create_default_context()

    try:
        with smtplib.SMTP_SSL(SMTP_SERVER, SMTP_PORT, context=context) as server:
            server.login(SENDER_EMAIL, SENDER_PASSWORD)
            server.sendmail(SENDER_EMAIL, [TARGET_EMAIL], msg.as_string())
        print("邮件发送成功")
    except Exception as e:
        print("邮件发送失败：", e)

send_email("网站用户反馈", html_content)
