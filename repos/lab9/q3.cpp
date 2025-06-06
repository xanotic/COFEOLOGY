#include <iostream>
using namespace std;

double calculateTotal(char itemcode, int quantity){
    double price;
    if (itemcode == 'A')
        price = 1.20;
    else if (itemcode == 'B')
        price = 1.50;
    else if (itemcode == 'C')
        price = 2.00;
    else{
        cout << "item Code is not valid";
        return 0;}

    return price * quantity;
}

void printCheck(double totalprice){
    cout << "Total: RM" << totalprice;
}

int main() {

    char itemcode;
    int quantity;
    double totalPrice;

    cout << "Menu:\n";
    cout << "A: Muffin - RM1.20\n";
    cout << "B: Egg Sandwich - RM1.50\n";
    cout << "C: Ginger Bread - RM2.00\n";
    cout << "Please enter the item code : ";
    cin >> itemcode;
    cout << "Please enter the quantity : ";
    cin >> quantity;

    totalPrice = calculateTotal(itemcode, quantity);
    if (totalPrice > 0) { 
        printCheck(totalPrice);
    }

    return 0;
}
    





