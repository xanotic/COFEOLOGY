#include <iostream>
using namespace std;

int main()
{
    double itemCost;
    double itemNumber;
    double discRate;

    cout << "Cost per item : ";
    cin >> itemCost;

    cout << "Number of the item : ";
    cin >> itemNumber;

    cout << "Discount rate of the item (enter in decimal ex: 5% = 0.05): ";
    cin >> discRate;

    double totalCost = itemCost * itemNumber;
    double totalDisc = totalCost - (discRate * totalCost);
    double taxRate = 0.06;
    double taxDue = totalDisc * taxRate;
    double amountDue = totalDisc + taxDue;

    cout << "Total item cost : " << totalCost << "\n";
    cout << "Total cost after discount : " << totalDisc << "\n";
    cout << "Tax due : " << taxDue << "\n";
    cout << "Amount due : " << amountDue << "\n";

    return 0;
}