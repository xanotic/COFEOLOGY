
#include <iostream>
using namespace std;

int main() {
    int quantity;
    double priceItem, totalAll = 0.0, totalCustomer;

    cout << "Enter quantity of items(0 to end): ";
    cin >> quantity;
    cout << "Enter  price per item: ";
    cin >> priceItem;
    while (quantity != 0) {
       
        totalCustomer = quantity * priceItem;
        totalAll += totalCustomer;
        
        cout << "Total for this customer: $" << totalCustomer << endl;
        cout << "Enter quantity of items and price per item (0 to end): " << endl;
        cin >> quantity;
    }

    cout << "Total amount paid by all customers: $" << totalAll << endl;
    
    return 0;
}
