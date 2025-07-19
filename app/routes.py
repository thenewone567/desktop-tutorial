from app import app
from flask import render_template, request, redirect, url_for, session, make_response
import json
from app.decorators import role_required
from weasyprint import HTML

def get_users():
    with open('data/users.json') as f:
        return json.load(f)

def get_products():
    with open('data/products.json') as f:
        return json.load(f)

def save_products(products):
    with open('data/products.json', 'w') as f:
        json.dump(products, f, indent=4)

def get_purchases():
    with open('data/purchases.json') as f:
        return json.load(f)

def save_purchases(purchases):
    with open('data/purchases.json', 'w') as f:
        json.dump(purchases, f, indent=4)

def get_sales():
    with open('data/sales.json') as f:
        return json.load(f)

def save_sales(sales):
    with open('data/sales.json', 'w') as f:
        json.dump(sales, f, indent=4)

def get_sales_returns():
    with open('data/sales_returns.json') as f:
        return json.load(f)

def save_sales_returns(sales_returns):
    with open('data/sales_returns.json', 'w') as f:
        json.dump(sales_returns, f, indent=4)

def get_purchase_returns():
    with open('data/purchase_returns.json') as f:
        return json.load(f)

def save_purchase_returns(purchase_returns):
    with open('data/purchase_returns.json', 'w') as f:
        json.dump(purchase_returns, f, indent=4)

@app.route('/')
def index():
    if 'username' in session:
        return render_template('index.html', username=session['username'])
    return redirect(url_for('login'))

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        users = get_users()
        for user in users:
            if user['username'] == username and user['password'] == password:
                session['username'] = username
                session['role'] = user['role']
                return redirect(url_for('index'))
        return 'Invalid username or password'
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.pop('username', None)
    session.pop('role', None)
    return redirect(url_for('login'))

@app.route('/admin')
@role_required('Admin')
def admin():
    return 'Admin page'

@app.route('/inventory')
@role_required('Admin')
def inventory():
    products = get_products()
    return render_template('inventory.html', products=products)

@app.route('/add_product', methods=['GET', 'POST'])
@role_required('Admin')
def add_product():
    if request.method == 'POST':
        products = get_products()
        new_product = {
            "id": len(products) + 1,
            "name": request.form['name'],
            "sku": request.form['sku'],
            "barcode": request.form['barcode'],
            "category": request.form['category'],
            "purchase_rate": float(request.form['purchase_rate']),
            "selling_rate": float(request.form['selling_rate']),
            "quantity": int(request.form['quantity']),
            "aisle": request.form['aisle'],
            "rack": request.form['rack'],
            "bin": request.form['bin']
        }
        products.append(new_product)
        save_products(products)
        return redirect(url_for('inventory'))
    return render_template('add_product.html')

@app.route('/edit_product/<int:product_id>', methods=['GET', 'POST'])
@role_required('Admin')
def edit_product(product_id):
    products = get_products()
    product = next((p for p in products if p['id'] == product_id), None)
    if product is None:
        return 'Product not found'

    if request.method == 'POST':
        product['name'] = request.form['name']
        product['sku'] = request.form['sku']
        product['barcode'] = request.form['barcode']
        product['category'] = request.form['category']
        product['purchase_rate'] = float(request.form['purchase_rate'])
        product['selling_rate'] = float(request.form['selling_rate'])
        product['quantity'] = int(request.form['quantity'])
        product['aisle'] = request.form['aisle']
        product['rack'] = request.form['rack']
        product['bin'] = request.form['bin']
        save_products(products)
        return redirect(url_for('inventory'))

    return render_template('edit_product.html', product=product)

@app.route('/delete_product/<int:product_id>')
@role_required('Admin')
def delete_product(product_id):
    products = get_products()
    products = [p for p in products if p['id'] != product_id]
    save_products(products)
    return redirect(url_for('inventory'))

@app.route('/purchases')
@role_required('Admin')
def purchases():
    purchases = get_purchases()
    return render_template('purchases.html', purchases=purchases)

@app.route('/add_purchase', methods=['GET', 'POST'])
@role_required('Admin')
def add_purchase():
    if request.method == 'POST':
        purchases = get_purchases()
        new_purchase = {
            "id": len(purchases) + 1,
            "supplier": request.form['supplier'],
            "date": request.form['date'],
            "items": []
        }

        product_ids = request.form.getlist('product_id[]')
        quantities = request.form.getlist('quantity[]')
        rates = request.form.getlist('rate[]')

        for i in range(len(product_ids)):
            new_purchase['items'].append({
                "product_id": int(product_ids[i]),
                "quantity": int(quantities[i]),
                "rate": float(rates[i])
            })

            # Update product stock
            products = get_products()
            product = next((p for p in products if p['id'] == int(product_ids[i])), None)
            if product:
                product['quantity'] += int(quantities[i])
                save_products(products)

        purchases.append(new_purchase)
        save_purchases(purchases)
        return redirect(url_for('purchases'))

    products = get_products()
    return render_template('add_purchase.html', products=products)

@app.route('/sales')
@role_required('Cashier')
def sales():
    sales = get_sales()
    return render_template('sales.html', sales=sales)

@app.route('/add_sale', methods=['GET', 'POST'])
@role_required('Cashier')
def add_sale():
    if request.method == 'POST':
        sales = get_sales()
        new_sale = {
            "id": len(sales) + 1,
            "customer": request.form['customer'],
            "date": request.form['date'],
            "items": [],
            "total": 0,
            "discount": float(request.form['discount']),
            "tax": float(request.form['tax']),
            "grand_total": 0,
            "payment_method": request.form['payment_method']
        }

        product_ids = request.form.getlist('product_id[]')
        quantities = request.form.getlist('quantity[]')

        total = 0
        for i in range(len(product_ids)):
            products = get_products()
            product = next((p for p in products if p['id'] == int(product_ids[i])), None)
            if product and product['quantity'] >= int(quantities[i]):
                rate = product['selling_rate']
                total += int(quantities[i]) * rate
                new_sale['items'].append({
                    "product_id": int(product_ids[i]),
                    "quantity": int(quantities[i]),
                    "rate": rate
                })

                # Update product stock
                product['quantity'] -= int(quantities[i])
                save_products(products)
            else:
                return "Not enough stock for product ID " + product_ids[i]

        new_sale['total'] = total
        new_sale['grand_total'] = total - new_sale['discount'] + new_sale['tax']
        sales.append(new_sale)
        save_sales(sales)
        return redirect(url_for('sales'))

    products = get_products()
    return render_template('add_sale.html', products=products)

@app.route('/sales_returns')
@role_required('Cashier')
def sales_returns():
    sales_returns = get_sales_returns()
    return render_template('sales_returns.html', sales_returns=sales_returns)

@app.route('/add_sales_return', methods=['GET', 'POST'])
@role_required('Cashier')
def add_sales_return():
    if request.method == 'POST':
        sales_returns = get_sales_returns()
        new_sales_return = {
            "id": len(sales_returns) + 1,
            "sale_id": int(request.form['sale_id']),
            "date": request.form['date'],
            "items": []
        }

        product_ids = request.form.getlist('product_id[]')
        quantities = request.form.getlist('quantity[]')

        for i in range(len(product_ids)):
            new_sales_return['items'].append({
                "product_id": int(product_ids[i]),
                "quantity": int(quantities[i])
            })

            # Update product stock
            products = get_products()
            product = next((p for p in products if p['id'] == int(product_ids[i])), None)
            if product:
                product['quantity'] += int(quantities[i])
                save_products(products)

        sales_returns.append(new_sales_return)
        save_sales_returns(sales_returns)
        return redirect(url_for('sales_returns'))

    sales = get_sales()
    products = get_products()
    return render_template('add_sales_return.html', sales=sales, products=products)

@app.route('/purchase_returns')
@role_required('Admin')
def purchase_returns():
    purchase_returns = get_purchase_returns()
    return render_template('purchase_returns.html', purchase_returns=purchase_returns)

@app.route('/add_purchase_return', methods=['GET', 'POST'])
@role_required('Admin')
def add_purchase_return():
    if request.method == 'POST':
        purchase_returns = get_purchase_returns()
        new_purchase_return = {
            "id": len(purchase_returns) + 1,
            "purchase_id": int(request.form['purchase_id']),
            "date": request.form['date'],
            "items": []
        }

        product_ids = request.form.getlist('product_id[]')
        quantities = request.form.getlist('quantity[]')

        for i in range(len(product_ids)):
            new_purchase_return['items'].append({
                "product_id": int(product_ids[i]),
                "quantity": int(quantities[i])
            })

            # Update product stock
            products = get_products()
            product = next((p for p in products if p['id'] == int(product_ids[i])), None)
            if product:
                product['quantity'] -= int(quantities[i])
                save_products(products)

        purchase_returns.append(new_purchase_return)
        save_purchase_returns(purchase_returns)
        return redirect(url_for('purchase_returns'))

    purchases = get_purchases()
    products = get_products()
    return render_template('add_purchase_return.html', purchases=purchases, products=products)

@app.route('/invoice/<int:sale_id>')
def invoice(sale_id):
    sales = get_sales()
    sale = next((s for s in sales if s['id'] == sale_id), None)
    if sale is None:
        return 'Sale not found'

    products = get_products()
    rendered_template = render_template('invoice.html', sale=sale, products=products)
    pdf = HTML(string=rendered_template).write_pdf()

    response = make_response(pdf)
    response.headers['Content-Type'] = 'application/pdf'
    response.headers['Content-Disposition'] = f'inline; filename=invoice_{sale_id}.pdf'

    return response

from datetime import datetime, timedelta

@app.route('/reports')
@role_required('Admin')
def reports():
    return render_template('reports.html')

from collections import Counter

@app.route('/dashboard')
@role_required('Admin')
def dashboard():
    sales = get_sales()
    products = get_products()

    # Monthly sales data
    monthly_sales_data = [0] * 12
    for sale in sales:
        month = datetime.strptime(sale['date'], '%Y-%m-%d').month
        monthly_sales_data[month - 1] += sale['grand_total']

    # Top selling products data
    sold_products = []
    for sale in sales:
        for item in sale['items']:
            sold_products.extend([item['product_id']] * item['quantity'])

    top_products = Counter(sold_products).most_common(5)
    top_products_labels = [get_products_by_id(p[0])['name'] for p in top_products]
    top_products_data = [p[1] for p in top_products]

    # Stock levels data
    stock_labels = [p['name'] for p in products]
    stock_data = [p['quantity'] for p in products]

    return render_template('dashboard.html',
                           monthly_sales_data=json.dumps(monthly_sales_data),
                           top_products_labels=json.dumps(top_products_labels),
                           top_products_data=json.dumps(top_products_data),
                           stock_labels=json.dumps(stock_labels),
                           stock_data=json.dumps(stock_data))

def get_products_by_id(product_id):
    products = get_products()
    return next((p for p in products if p['id'] == product_id), None)

@app.route('/sales_report')
@role_required('Admin')
def sales_report():
    sales = get_sales()
    today = datetime.now().date()

    daily_sales = [s for s in sales if datetime.strptime(s['date'], '%Y-%m-%d').date() == today]
    weekly_sales = [s for s in sales if today - timedelta(days=7) <= datetime.strptime(s['date'], '%Y-%m-%d').date() <= today]
    monthly_sales = [s for s in sales if today.replace(day=1) <= datetime.strptime(s['date'], '%Y-%m-%d').date() <= today]

    return render_template('sales_report.html', daily_sales=daily_sales, weekly_sales=weekly_sales, monthly_sales=monthly_sales)

@app.route('/purchases_report')
@role_required('Admin')
def purchases_report():
    purchases = get_purchases()
    today = datetime.now().date()

    daily_purchases = [p for p in purchases if datetime.strptime(p['date'], '%Y-%m-%d').date() == today]
    weekly_purchases = [p for p in purchases if today - timedelta(days=7) <= datetime.strptime(p['date'], '%Y-%m-%d').date() <= today]
    monthly_purchases = [p for p in purchases if today.replace(day=1) <= datetime.strptime(p['date'], '%Y-%m-%d').date() <= today]

    return render_template('purchases_report.html', daily_purchases=daily_purchases, weekly_purchases=weekly_purchases, monthly_purchases=monthly_purchases)

@app.route('/stock_report')
@role_required('Admin')
def stock_report():
    products = get_products()
    return render_template('stock_report.html', products=products)

@app.route('/returns_report')
@role_required('Admin')
def returns_report():
    sales_returns = get_sales_returns()
    purchase_returns = get_purchase_returns()
    return render_template('returns_report.html', sales_returns=sales_returns, purchase_returns=purchase_returns)
