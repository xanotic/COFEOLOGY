#include <iostream>
using namespace std;

int main()
{
    int itemCode, itemQuantity;
    float itemPrice, totalPrice;

    cout << "Enter item code (1,2,3): ";
    cin >> itemCode;

    if (itemCode == 1)
    {
        itemPrice = 100;
    }
    else if (itemCode == 2)
    {
        itemPrice = 200;
    }
    else if (itemCode == 3)
    {
        itemPrice = 300;
    }

    cout << "Enter item quantity: ";
    cin >> itemQuantity;

    totalPrice = itemPrice * itemQuantity;
    cout << "Total price: RM" << totalPrice << endl;

    return 0;
}