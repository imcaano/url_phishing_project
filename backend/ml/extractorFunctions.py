# importing required packages for Address Bar Based feature Extraction
from urllib.parse import urlparse, urlencode, unquote
import re
# importing required packages for Domain Based Feature Extraction
from datetime import datetime

# 2.Checks for IP address in URL (Have_IP)
def havingIP(url):
    ip_pattern = r"\b(?:\d{1,3}\.){3}\d{1,3}\b"
    match = re.search(ip_pattern, url)
    if match:
        return 1
    return 0

def haveAtSign(url):
    if "@" in url:
        at = 1
    else:
        at = 0
    return at

def getLength(url):
    return len(url)

def getDepth(url):
    s = urlparse(url).path.split('/')
    depth = 0
    for j in range(len(s)):
        if len(s[j]) != 0:
            depth = depth+1
    return depth

shortening_services = r"bit\.ly|goo\.gl|shorte\.st|go2l\.ink|x\.co|ow\.ly|t\.co|tinyurl|tr\.im|is\.gd|cli\.gs|" \
                      r"yfrog\.com|migre\.me|ff\.im|tiny\.cc|url4\.eu|twit\.ac|su\.pr|twurl\.nl|snipurl\.com|" \
                      r"short\.to|BudURL\.com|ping\.fm|post\.ly|Just\.as|bkite\.com|snipr\.com|fic\.kr|loopt\.us|" \
                      r"doiop\.com|short\.ie|kl\.am|wp\.me|rubyurl\.com|om\.ly|to\.ly|bit\.do|t\.co|lnkd\.in|db\.tt|" \
                      r"qr\.ae|adf\.ly|goo\.gl|bitly\.com|cur\.lv|tinyurl\.com|ow\.ly|bit\.ly|ity\.im|q\.gs|is\.gd|" \
                      r"po\.st|bc\.vc|twitthis\.com|u\.to|j\.mp|buzurl\.com|cutt\.us|u\.bb|yourls\.org|x\.co|" \
                      r"prettylinkpro\.com|scrnch\.me|filoops\.info|vzturl\.com|qr\.net|1url\.com|tweez\.me|v\.gd|" \
                      r"tr\.im|link\.zip\.net"

def tinyURL(url):
    match=re.search(shortening_services,url)
    if match:
        return 1
    else:
        return 0

def prefixSuffix(url):
    if '-' in urlparse(url).netloc:
        return 1            # phishing
    else:
        return 0            # legitimate

def no_of_dots(url):
    return url.count('.')

sensitiveWords = ["account", "confirm", "banking", "secure", "ebyisapi", "webscr", "signin", "mail",
                  "install", "toolbar", "backup", "paypal", "password", "username", "verify", "update",
                  "login", "support", "billing", "transaction", "security", "payment", "verify", "online",
                  "customer", "service", "accountupdate", "verification", "important", "confidential",
                  "limited", "access", "securitycheck", "verifyaccount", "information", "change", "notice"
                  "myaccount", "updateinfo", "loginsecure", "protect", "transaction", "identity", "member"
                  "personal", "actionrequired", "loginverify", "validate", "paymentupdate", "urgent"]

def sensitive_word(url):
    domain = urlparse(url).netloc
    for i in sensitiveWords:
        if i in domain:
            return 1
    return 0

def has_unicode(url):
    parsed_url = urlparse(url)
    netloc = parsed_url.netloc
    decoded_netloc = netloc.encode('latin1').decode('idna')
    unquoted_netloc = unquote(decoded_netloc)
    if unquoted_netloc != netloc:
        return 1
    return 0

def domainAge(domain_name):
    creation_date = domain_name.creation_date
    expiration_date = domain_name.expiration_date
    if (isinstance(creation_date,str) or isinstance(expiration_date,str)):
        try:
            creation_date = datetime.strptime(creation_date,'%Y-%m-%d')
            expiration_date = datetime.strptime(expiration_date,"%Y-%m-%d")
        except:
            return 1
    if ((expiration_date is None) or (creation_date is None)):
        return 1
    elif ((type(expiration_date) is list) or (type(creation_date) is list)):
        return 1
    else:
        ageofdomain = abs((expiration_date - creation_date).days)
        if ((ageofdomain/30) < 6):
            age = 1
        else:
            age = 0
    return age

def domainEnd(domain_name):
    expiration_date = domain_name.expiration_date
    if isinstance(expiration_date,str):
        try:
            expiration_date = datetime.strptime(expiration_date,"%Y-%m-%d")
        except:
            return 1
    if (expiration_date is None):
        return 1
    elif (type(expiration_date) is list):
        return 1
    else:
        today = datetime.now()
        end = abs((expiration_date - today).days)
        if ((end/30) < 6):
            end = 0
        else:
            end = 1
    return end

def iframe(response):
    if response == "":
        return 1
    else:
        if re.findall(r"[<iframe>|<frameBorder>]", response.text):
            return 0
        else:
            return 1

def mouseOver(response):
    if response == "" :
        return 1
    else:
        try:
            if re.findall("<script>.+onmouseover.+</script>", response.text):
                return 1
            else:
                return 0
        except:
            return 1

def forwarding(response):
    if response == "":
        return 1
    else:
        if len(response.history) <= 2:
            return 0
        else:
            return 1 