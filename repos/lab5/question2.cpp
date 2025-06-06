#include <iostream>
using namespace std;
#include <string>

int main()
{
    int movieCode, numberOfTicket;
    float newPrice;
    string movieTitle, paymentCode;

    cout << "Enter movie code: ";
    cin >> movieCode;
    cout << "Enter number of tickets: ";
    cin >> numberOfTicket;

    switch (movieCode)
    {
        case 1:
        newPrice = numberOfTicket * 20;
        movieTitle = "Frozen";
            break;
        default:
        newPrice = numberOfTicket * 25;
        movieTitle = "Spiderman";
            break;
    }

    cout << "Enter payment code (credit card or cash or debit card): ";
    cin.ignore();
    getline(cin, paymentCode);

    if (paymentCode == "Credit Card" || paymentCode == "credit card")
    {
        newPrice = newPrice * 1.10;
    }

    cout << movieTitle << " RM:" << newPrice;

    return 0;    
}