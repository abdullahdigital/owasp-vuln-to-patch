import requests

# ========== SETTINGS ==========
BASE_URL = 'http://localhost:8000'
COMMON_PATHS = ['login.php', 'admin.php', 'dashboard.php', 'index.php', 'user.php']
COMMON_PARAMS = ['debug', 'test', 'view', 'dump', 'dev', 'mode']

def scan():
    print("[*] Scanning common paths and parameters...\n")

    for path in COMMON_PATHS:
        url = f"{BASE_URL}/{path}"
        try:
            res = requests.get(url)
            if res.status_code == 200:
                print(f"[+] Found page: {url}")

                for param in COMMON_PARAMS:
                    debug_url = f"{url}?{param}=1"
                    debug_res = requests.get(debug_url)

                    if debug_res.status_code == 200 and 'users' in debug_res.text.lower():
                        print(f"[!!] Possible exposed debug endpoint: {debug_url}")
        except Exception as e:
            print(f"[-] Error checking {url}: {e}")

if __name__ == '__main__':
    scan()
