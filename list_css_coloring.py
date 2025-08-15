import os
import requests
import json
import smtplib
from email.mime.text import MIMEText
from email.header import Header

AUTH_TOKEN = "bce-v3/ALTAK-rSBxzUuXWUOcF6Ky965sA/b1e525453938b5dffb5386588d44b5e707faa3a2"
ROOT_DIR = "."  # Root directory to scan
OUTPUT_FILE = "analysis_output.md"
API_URL = "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions"

SMTP_CONFIG = {
    "SMTP_SERVER": "smtp.exmail.qq.com",
    "SMTP_PORT": 465,
    "SENDER_EMAIL": "service_a@zhiyongedu.net",
    "SENDER_PASSWORD": "Sweepy123",
    "SENDER_NAME": "Issue Feedback System",
    "TARGET_EMAILS": [
        ("service_a@zhiyongedu.net", "Administrator"),
        ("3442242644@qq.com", "Second Recipient")
    ]
}

def analyze_code(file_path, code_content):
    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {AUTH_TOKEN}"
    }

    messages = [
        {
            "role": "user",
            "content": (
                "You are a senior programming analysis expert. Please provide me the coloring information in the file. If the file is a css file, for each color(categorized by HEX) list where they're used. If it's a php file with HTML code, for each colored element whose color is defined within the code, tell me what it is and which color is it. Print your answer in Chinese markdown format.\n\n"
                f"File path: {file_path}\n\nPlease analyze the following code:\n```{os.path.splitext(file_path)[1][1:]}\n{code_content}\n```"
            )
        }
    ]

    payload = json.dumps({
        "temperature": 0.2,
        "top_p": 0.8,
        "penalty_score": 1,
        "enable_system_memory": False,
        "disable_search": False,
        "enable_citation": False,
        "response_format": "text",
        "messages": messages
    }, ensure_ascii=False)

    try:
        response = requests.post(API_URL, headers=headers, data=payload.encode("utf-8"))
        response.raise_for_status()
        result_json = response.json()
        content = result_json.get("result", "")
        print(f"Analysis completed: {file_path}")
        return content
    except Exception as e:
        print(f"Request failed: {file_path} => {e}")
        return f"**Request failed: {e}**"

def send_email(subject, body, smtp_config):
    msg = MIMEText(body, "plain", "utf-8")
    sender = smtp_config["SENDER_EMAIL"]
    sender_name = smtp_config["SENDER_NAME"]
    msg["From"] = Header(sender_name, "utf-8").encode() + f" <{sender}>"
    # Join multiple recipients for header
    recipients_header = ", ".join([f'{Header(name, "utf-8").encode()} <{email}>' for email, name in smtp_config["TARGET_EMAILS"]])
    msg["To"] = recipients_header
    msg["Subject"] = Header(subject, "utf-8")

    try:
        with smtplib.SMTP_SSL(smtp_config["SMTP_SERVER"], smtp_config["SMTP_PORT"]) as server:
            server.login(sender, smtp_config["SENDER_PASSWORD"])
            # Send to all recipients email addresses
            server.sendmail(sender, [email for email, _ in smtp_config["TARGET_EMAILS"]], msg.as_string())
        print(f"Email sent successfully: {subject}")
    except Exception as e:
        print(f"Failed to send email: {e}")

def traverse_and_analyze(root_dir):
    results = []
    processed_files = []
    count = 0

    for dirpath, dirnames, filenames in os.walk(root_dir):
        # Exclude .git directories
        dirnames[:] = [d for d in dirnames if d != ".git"]

        for filename in filenames:
            file_path = os.path.join(dirpath, filename)
            try:
                with open(file_path, "r", encoding="utf-8") as f:
                    content = f.read()
            except Exception as e:
                print(f"Failed to read: {file_path} => {e}")
                continue

            relative_path = os.path.relpath(file_path, root_dir)
            print(f"Analyzing: {relative_path}")
            analysis_result = analyze_code(relative_path, content)
            results.append(f"## {relative_path}\n\n{analysis_result}\n\n---\n\n")
            processed_files.append(relative_path)
            count += 1

            # Every 10 files, send progress email
            if count % 10 == 0:
                progress_body = "Files analyzed so far:\n\n" + "\n".join(processed_files)
                send_email(f"Progress update: {count} files analyzed", progress_body, SMTP_CONFIG)

    return "".join(results)

def main():
    print("Starting to scan and analyze files...")
    full_result = traverse_and_analyze(ROOT_DIR)

    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        f.write(full_result)

    print(f"Analysis completed, results saved to {OUTPUT_FILE}")
    print("Sending final email...")
    send_email("Code Analysis Results Summary", full_result, SMTP_CONFIG)

if __name__ == "__main__":
    main()
