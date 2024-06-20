import requests
import json
import datetime
import time
import yaml

with open('config.yaml', encoding='UTF-8') as f:
    _cfg = yaml.load(f, Loader=yaml.FullLoader)
APP_KEY = _cfg['APP_KEY']
APP_SECRET = _cfg['APP_SECRET']
ACCESS_TOKEN = ""
CANO = _cfg['CANO']
ACNT_PRDT_CD = _cfg['ACNT_PRDT_CD']
DISCORD_WEBHOOK_URL = _cfg['DISCORD_WEBHOOK_URL']
URL_BASE = _cfg['URL_BASE']

def send_message(msg):
    """디스코드 메시지 전송"""
    now = datetime.datetime.now()
    message = {"content": f"[{now.strftime('%Y-%m-%d %H:%M:%S')}] {str(msg)}"}
    requests.post(DISCORD_WEBHOOK_URL, data=message)
    print(message)

def get_access_token():
    """토큰 발급"""
    headers = {"content-type":"application/json"}
    body = {
        "grant_type":"client_credentials",
        "appkey":APP_KEY, 
        "appsecret":APP_SECRET
    }
    PATH = "oauth2/tokenP"
    URL = f"{URL_BASE}/{PATH}"
    res = requests.post(URL, headers=headers, data=json.dumps(body))
    if res.status_code == 200:
        global ACCESS_TOKEN
        ACCESS_TOKEN = res.json()["access_token"]
        return ACCESS_TOKEN
    else:
        send_message(f"Failed to get access token: {res.status_code}, {res.text}")
        return None

def hashkey(datas):
    """암호화"""
    PATH = "uapi/hashkey"
    URL = f"{URL_BASE}/{PATH}"
    headers = {
        'content-Type' : 'application/json',
        'appKey' : APP_KEY,
        'appSecret' : APP_SECRET,
    }
    res = requests.post(URL, headers=headers, data=json.dumps(datas))
    if res.status_code == 200:
        return res.json()["HASH"]
    else:
        send_message(f"Failed to get hashkey: {res.status_code}, {res.text}")
        return None

def get_current_price(stock_code):
    """현재가 조회"""
    global ACCESS_TOKEN
    url = f"{URL_BASE}/uapi/domestic-stock/v1/quotations/inquire-price"
    headers = {
        "Content-Type": "application/json; charset=utf-8",
        "authorization": f"Bearer {ACCESS_TOKEN}",
        "appKey": APP_KEY,
        "appSecret": APP_SECRET,
        "tr_id": "FHKST01010100"
    }
    params = {"fid_cond_mrkt_div_code": "J", "fid_input_iscd": stock_code}
    response = requests.get(url, headers=headers, params=params)
    time.sleep(0.5)
    if response.status_code == 200 and 'output' in response.json():
        return int(response.json()['output']['stck_prpr'])
    else:
        send_message(f"Failed to retrieve current price for {stock_code}: {response.status_code}, {response.text}")
        return None

def calculate_rsi(prices, periods=14):
    """RSI 계산"""
    closes = [int(price['stck_clpr']) for price in prices]
    deltas = [closes[i] - closes[i-1] for i in range(1, len(closes))]
    gains = [x if x > 0 else 0 for x in deltas]
    losses = [-x if x < 0 else 0 for x in deltas]

    avg_gain = sum(gains[:periods]) / periods
    avg_loss = sum(losses[:periods]) / periods

    rs = avg_gain / avg_loss if avg_loss != 0 else 0
    rsi = 100 - (100 / (1 + rs))

    return rsi

def get_daily_prices(stock_code, start_date, end_date):
    """주식의 일별 가격 데이터를 조회합니다."""
    url = f"{URL_BASE}/uapi/domestic-stock/v1/quotations/inquire-daily-price"
    headers = {
        "Content-Type": "application/json",
        "authorization": f"Bearer {ACCESS_TOKEN}",
        "appKey": APP_KEY,
        "appSecret": APP_SECRET,
        "tr_id": "FHKST01010400"
    }
    params = {
        "fid_cond_mrkt_div_code": "J",
        "fid_input_iscd": stock_code,
        "fid_input_date_1": start_date,
        "fid_input_date_2": end_date,
        "fid_org_adj_prc": "1",
        "fid_period_div_code": "D"
    }
    response = requests.get(url, headers=headers, params=params)
    if response.status_code == 200 and 'output' in response.json():
        return [data for data in response.json()['output']]
    else:
        send_message(f"Failed to retrieve daily prices for {stock_code}: {response.status_code}, {response.text}")
        return None

def get_target_price(code="005930"):
    """변동성 돌파 전략으로 매수 목표가 조회"""
    PATH = "uapi/domestic-stock/v1/quotations/inquire-daily-price"
    URL = f"{URL_BASE}/{PATH}"
    headers = {"Content-Type":"application/json", 
        "authorization": f"Bearer {ACCESS_TOKEN}",
        "appKey":APP_KEY,
        "appSecret":APP_SECRET,
        "tr_id":"FHKST01010400"}
    params = {
    "fid_cond_mrkt_div_code":"J",
    "fid_input_iscd":code,
    "fid_org_adj_prc":"1",
    "fid_period_div_code":"D"
    }
    res = requests.get(URL, headers=headers, params=params)
    if res.status_code == 200 and 'output' in res.json():
        output = res.json()['output']
        if output:
            stck_oprc = int(output[0]['stck_oprc']) #오늘 시가
            stck_hgpr = int(output[1]['stck_hgpr']) #전일 고가
            stck_lwpr = int(output[1]['stck_lwpr']) #전일 저가
            target_price = stck_oprc + (stck_hgpr - stck_lwpr) * 0.5
            return target_price
    send_message(f"Failed to retrieve target price for {code}: {res.status_code}, {res.text}")
    return None

def get_stock_balance():
    """주식 잔고조회"""
    PATH = "uapi/domestic-stock/v1/trading/inquire-balance"
    URL = f"{URL_BASE}/{PATH}"
    headers = {"Content-Type":"application/json", 
        "authorization":f"Bearer {ACCESS_TOKEN}",
        "appKey":APP_KEY,
        "appSecret":APP_SECRET,
        "tr_id":"TTTC8434R",
        "custtype":"P",
    }
    params = {
        "CANO": CANO,
        "ACNT_PRDT_CD": ACNT_PRDT_CD,
        "AFHR_FLPR_YN": "N",
        "OFL_YN": "",
        "INQR_DVSN": "02",
        "UNPR_DVSN": "01",
        "FUND_STTL_ICLD_YN": "N",
        "FNCG_AMT_AUTO_RDPT_YN": "N",
        "PRCS_DVSN": "01",
        "CTX_AREA_FK100": "",
        "CTX_AREA_NK100": ""
    }
    res = requests.get(URL, headers=headers, params=params)
    if res.status_code == 200 and 'output1' in res.json() and 'output2' in res.json():
        stock_list = res.json()['output1']
        evaluation = res.json()['output2']
        stock_dict = {}
        send_message(f"====주식 보유잔고====")
        for stock in stock_list:
            if int(stock['hldg_qty']) > 0:
                stock_dict[stock['pdno']] = stock['hldg_qty']
                send_message(f"{stock['prdt_name']}({stock['pdno']}): {stock['hldg_qty']}주")
                time.sleep(0.1)
        send_message(f"주식 평가 금액: {evaluation[0]['scts_evlu_amt']}원")
        time.sleep(0.1)
        send_message(f"평가 손익 합계: {evaluation[0]['evlu_pfls_smtl_amt']}원")
        time.sleep(0.1)
        send_message(f"총 평가 금액: {evaluation[0]['tot_evlu_amt']}원")
        time.sleep(0.1)
        send_message(f"=================")
        return stock_dict
    send_message(f"Failed to retrieve stock balance: {res.status_code}, {res.text}")
    return {}

def get_balance():
    """현금 잔고조회"""
    PATH = "uapi/domestic-stock/v1/trading/inquire-psbl-order"
    URL = f"{URL_BASE}/{PATH}"
    headers = {"Content-Type":"application/json", 
        "authorization":f"Bearer {ACCESS_TOKEN}",
        "appKey":APP_KEY,
        "appSecret":APP_SECRET,
        "tr_id":"TTTC8908R",
        "custtype":"P",
    }
    params = {
        "CANO": CANO,
        "ACNT_PRDT_CD": ACNT_PRDT_CD,
        "PDNO": "005930",
        "ORD_UNPR": "65500",
        "ORD_DVSN": "01",
        "CMA_EVLU_AMT_ICLD_YN": "Y",
        "OVRS_ICLD_YN": "Y"
    }
    res = requests.get(URL, headers=headers, params=params)
    if res.status_code == 200 and 'output' in res.json():
        cash = res.json()['output']['ord_psbl_cash']
        send_message(f"주문 가능 현금 잔고: {cash}원")
        return int(cash)
    send_message(f"Failed to retrieve balance: {res.status_code}, {res.text}")
    return 0

def buy(code="005930", qty="1"):
    """주식 시장가 매수"""  
    PATH = "uapi/domestic-stock/v1/trading/order-cash"
    URL = f"{URL_BASE}/{PATH}"
    data = {
        "CANO": CANO,
        "ACNT_PRDT_CD": ACNT_PRDT_CD,
        "PDNO": code,
        "ORD_DVSN": "01",
        "ORD_QTY": str(int(qty)),
        "ORD_UNPR": "0",
    }
    headers = {"Content-Type":"application/json", 
        "authorization":f"Bearer {ACCESS_TOKEN}",
        "appKey":APP_KEY,
        "appSecret":APP_SECRET,
        "tr_id":"TTTC0802U",
        "custtype":"P",
        "hashkey" : hashkey(data)
    }
    res = requests.post(URL, headers=headers, data=json.dumps(data))
    if res.status_code == 200 and 'rt_cd' in res.json() and res.json()['rt_cd'] == '0':
        send_message(f"[매수 성공]{str(res.json())}")
        return True
    else:
        send_message(f"[매수 실패]{str(res.json())}")
        return False

def sell(code="005930", qty="1"):
    """주식 시장가 매도"""
    PATH = "uapi/domestic-stock/v1/trading/order-cash"
    URL = f"{URL_BASE}/{PATH}"
    data = {
        "CANO": CANO,
        "ACNT_PRDT_CD": ACNT_PRDT_CD,
        "PDNO": code,
        "ORD_DVSN": "01",
        "ORD_QTY": qty,
        "ORD_UNPR": "0",
    }
    headers = {"Content-Type":"application/json", 
        "authorization":f"Bearer {ACCESS_TOKEN}",
        "appKey":APP_KEY,
        "appSecret":APP_SECRET,
        "tr_id":"TTTC0801U",
        "custtype":"P",
        "hashkey" : hashkey(data)
    }
    res = requests.post(URL, headers=headers, data=json.dumps(data))
    if res.status_code == 200 and 'rt_cd' in res.json() and res.json()['rt_cd'] == '0':
        send_message(f"[매도 성공]{str(res.json())}")
        return True
    else:
        send_message(f"[매도 실패]{str(res.json())}")
        return False

def monitor_stocks(stocks):
    """주식 모니터링 및 손절매"""
    for stock in stocks:
        current_price = get_current_price(stock[1])
        if current_price:
            change_percent = ((current_price - stock[2]) / stock[2]) * 100
            if change_percent <= -10:
                sell(stock[1], stock[3])  # 손절매
                send_message(f"손절매: {stock[0]} {stock[3]}주, 현재 가격: {current_price}원")

def main():
    global ACCESS_TOKEN
    ACCESS_TOKEN = get_access_token()
    if not ACCESS_TOKEN:
        return

    jongTarget = [
        { 'num': 1 ,'iscd':'200350', 'name':'래몽래인' },
        { 'num': 2 ,'iscd':'950130', 'name':'엑세스바이오'},
        { 'num': 3 ,'iscd':'198440', 'name':'고려시멘트'},
        { 'num': 4 ,'iscd':'219420', 'name':'링크제니시스'},
        { 'num': 5 ,'iscd':'215480', 'name':'토박스코리아'},
        { 'num': 6 ,'iscd':'037070', 'name':'파세코'},
        { 'num': 7 ,'iscd':'139670', 'name':'키네마스터'},
        { 'num': 8 ,'iscd':'030960', 'name':'양지사'},
        { 'num': 9 ,'iscd':'189300', 'name':'인텔리안테크'},
        { 'num': 10 ,'iscd':'026150', 'name':'특수건설'},
        { 'num': 11 ,'iscd':'014970', 'name':'삼륭물산'},
        { 'num': 12 ,'iscd':'014910', 'name':'성문전자'},
        { 'num': 13 ,'iscd':'234100', 'name':'폴라리스세원'},
        { 'num': 14 ,'iscd':'051380', 'name':'피씨디렉트' },
        { 'num': 15 ,'iscd':'101360', 'name':'이엔드디'},
        { 'num': 16 ,'iscd':'900120', 'name':'씨케이에이치' },
        { 'num': 17 ,'iscd':'004720', 'name':'팜젠사이언스' },
        { 'num': 18 ,'iscd':'196700', 'name':'웹스'},
        { 'num': 19 ,'iscd':'041020', 'name':'폴라리스오피스'},
        { 'num': 20 ,'iscd':'027580', 'name':'상보' },
        { 'num': 21 ,'iscd':'114630', 'name':'폴라리스우노'},
        { 'num': 22 ,'iscd':'067630', 'name':'HLB생명과학' },
        { 'num': 23 ,'iscd':'203400', 'name':'에이비온'},
        { 'num': 24 ,'iscd':'051160', 'name':'지어소프트' },
        { 'num': 26 ,'iscd':'073570', 'name':'WI' },
        { 'num': 27 ,'iscd':'074610', 'name':'이엔플러스'},
        { 'num': 28 ,'iscd':'222160', 'name':'바이옵트로'},
        { 'num': 29 ,'iscd':'003720', 'name':'삼영화학' },
        { 'num': 30 ,'iscd':'153460', 'name':'네이블' },
        { 'num': 31 ,'iscd':'121850', 'name':'코이즈'},
        { 'num': 32 ,'iscd':'032820', 'name':'우리기술' },
        { 'num': 33 ,'iscd':'101140', 'name':'인바이오젠'},
        { 'num': 34 ,'iscd':'203650', 'name':'드림시큐리티'},
        { 'num': 35 ,'iscd':'076080', 'name':'웰크론한텍'},
        { 'num': 36 ,'iscd':'016920', 'name':'카스' },
        { 'num': 37 ,'iscd':'006110', 'name':'삼아알미늄'},
        { 'num': 38 ,'iscd':'094940', 'name':'푸른기술'},
        { 'num': 39 ,'iscd':'031860', 'name':'엔에스엔'},
        { 'num': 40 ,'iscd':'003580', 'name':'HLB글로벌' },
        { 'num': 41 ,'iscd':'020120', 'name':'키다리스튜디오'},
        { 'num': 42 ,'iscd':'109820', 'name':'진매트릭스' },
        { 'num': 43 ,'iscd':'094360', 'name':'칩스앤미디어'},
        { 'num': 44 ,'iscd':'256840', 'name':'한국비엔씨'},
        { 'num': 45 ,'iscd':'054090', 'name':'삼진엘앤디'},
        { 'num': 46 ,'iscd':'023150', 'name':'MH에탄올' },
        { 'num': 47 ,'iscd':'224110', 'name':'에이텍티앤'},
        { 'num': 48 ,'iscd':'230980', 'name':'에이트원' },
        { 'num': 49 ,'iscd':'101670', 'name':'코리아에스이'},
        { 'num': 50 ,'iscd':'189860', 'name':'서전기전' }
    ]

    selected_stocks = []
    for stock in jongTarget:
        time.sleep(1)  # API 호출 제한을 관리하기 위해 딜레이 추가
        target_price = get_target_price(stock['iscd'])
        current_price = get_current_price(stock['iscd'])
        if current_price and target_price and current_price > target_price:
            end_date = datetime.datetime.today().strftime('%Y%m%d')
            start_date = (datetime.datetime.today() - datetime.timedelta(days=30)).strftime('%Y%m%d')
            daily_prices = get_daily_prices(stock['iscd'], start_date, end_date)
            if daily_prices:  # 일별 가격 데이터가 있는지 확인
                rsi = calculate_rsi(daily_prices)
                if 30 <= rsi <= 70:  # RSI가 원하는 범위 내에 있는지 확인
                    selected_stocks.append((stock['name'], stock['iscd'], current_price, 0, rsi))  # 초기 매수가 0
                    send_message(f"Selected Stock: {stock['name']} - Current Price: {current_price}, Target Price: {target_price}, RSI: {rsi}")

    # RSI 범위 내에서 현재 가격이 높은 상위 5개 주식을 Discord로 전송
    top_stocks = sorted(selected_stocks, key=lambda x: -x[2])[:5]
    for stock in top_stocks:
        send_message(f"Top Stock: {stock[0]} - Current Price: {stock[2]}, Target Price: {stock[3]}, RSI: {stock[4]}")

    # 매수
    total_cash = get_balance()  # 보유 현금 조회
    if len(top_stocks) > 0:
        buy_amount = total_cash / len(top_stocks)  # 종목별 주문 금액 계산
        for stock in top_stocks:
            buy_qty = int(buy_amount // stock[2])
            if buy_qty > 0:
                result = buy(stock[1], buy_qty)
                if result:
                    send_message(f"매수 성공: {stock[0]} {buy_qty}주")
                    stock = (stock[0], stock[1], stock[2], buy_qty, stock[4])  # 매수가 업데이트

    while True:
        t_now = datetime.datetime.now()
        t_9 = t_now.replace(hour=9, minute=0, second=0, microsecond=0)
        t_sell = t_now.replace(hour=15, minute=15, second=0, microsecond=0)
        t_exit = t_now.replace(hour=15, minute=20, second=0, microsecond=0)
        today = datetime.datetime.today().weekday()
        if today == 5 or today == 6:  # 토요일이나 일요일이면 자동 종료
            send_message("주말이므로 프로그램을 종료합니다.")
            break
        if t_now >= t_exit:  # PM 03:20 ~ :프로그램 종료
            send_message("프로그램을 종료합니다.")
            break

        if t_now >= t_sell:  # PM 03:15 ~ PM 03:20 : 일괄 매도
            stock_dict = get_stock_balance()
            for sym, qty in stock_dict.items():
                sell(sym, qty)
            break

        if t_now.minute % 30 == 0:  # 매 30분마다 가격 모니터링
            monitor_stocks(top_stocks)
            time.sleep(60)  # 한 번 모니터링 후 1분 대기

if __name__ == "__main__":
    main()
