#include <iostream>
#include <iomanip>
#include <string>

using namespace std;

void getInput(char &categoryCode, string &productCode, int &quantity, double &price) {
    cout <<"\n" <<"Enter category : ";
    cin >> categoryCode;
    cout << "Enter Product Code : ";
    cin >> productCode;
    cout << "Enter Quantity : ";
    cin >> quantity;

    if (categoryCode == 'A') {
        if (productCode == "11-002")
            price = 4.50;
        else if (productCode == "11-076")
            price = 3.00;
        else if (productCode == "12-111")
            price = 2.50;
    }
    else if (categoryCode == 'B') {
        if (productCode == "08-388")
            price = 1.50;
        else if (productCode == "08-368")
            price = 5.00;
        else if (productCode == "06-121")
            price = 1.00;
    }
}

double calcTotalAmount(double &totalAmount, string &description, string &prodDescription, char categoryCode, string productCode, int quantity) {
    double price = 0.0;
    if (categoryCode == 'A') {
        if (productCode == "11-002") {
            price = 4.50;
            prodDescription = "Sport medal";
        } else if (productCode == "11-076") {
            price = 3.00;
            prodDescription = "Keychain";
        } else if (productCode == "12-111") {
            price = 2.50;
            prodDescription = "Coin plate";
        }
        description = "Metal Craft";
    } else if (categoryCode == 'B') {
        if (productCode == "08-388") {
            price = 1.50;
            prodDescription = "Sticker";
        } else if (productCode == "08-368") {
            price = 5.00;
            prodDescription = "Mug";
        } else if (productCode == "06-121") {
            price = 1.00;
            prodDescription = "Postcard";
        }
        description = "Gift and Craft";
    }
    double itemTotal = price * quantity;
    totalAmount += itemTotal;
    return itemTotal;
}

int main() {
    int numCategory, quantity;
    char categoryCode;
    string productCode, description, prodDescription;
    double totalAmount = 0.0, itemTotal, price;

    cout << "How many categories to buy ? : ";
    cin >> numCategory;

    for (int i = 0; i < numCategory; i++) {
        getInput(categoryCode, productCode, quantity, price); 
        itemTotal = calcTotalAmount(totalAmount, description, prodDescription, categoryCode, productCode, quantity);
        cout << "Total Amount " << ": RM " << fixed << setprecision(2) << itemTotal << endl;
    }

    cout << "Total ALL: RM " << fixed << setprecision(2) << totalAmount << endl;

    return 0;
}