#include <iostream>
#include <string>
#include <iomanip>
using namespace std;

int main() {
    string name, item, contactnum;
    double fee = 0, rental = 0, ticket_price = 0, totalrental = 0; 
    int ticketnum, age, quantity, child = 0, adult = 0, senior = 0;
    char citizenship, facilities;

    cout << "Payer's name: ";
    getline(cin, name);
    cout << "Payer's contact number: ";
    getline(cin, contactnum);
    cout << "Numbers of tickets needed: ";
    cin >> ticketnum;

    for (int i = 1; i <= ticketnum; i++) {
        cout << "Enter age for visitor [" << i << "]: ";
        cin >> age;
        cout << "Citizenship [M-Malaysian | F-Foreigner]:";
        cin >> citizenship;

        if (age <= 12) {
            ticket_price = 23;
            child++;
        } else if (age >= 13 && age <= 59) {
            ticket_price = 33;
            adult++;
        } else {
            ticket_price = 20;
            senior++;
        }

        if (citizenship == 'F' || citizenship == 'f') {
            ticket_price += 0.15 * ticket_price;
        }

        fee += ticket_price;
    }

    while (true) {
        cout << "Do you want to rent any facilities? [Y-Yes | N-No]: ";
        cin >> facilities;

        if (facilities == 'N' || facilities == 'n')
            break;

        cout << "Enter item id: ";
        cin >> item;

        cout << "Enter quantity: ";
        cin >> quantity;

        if (item == "ST")
            rental += 15 * quantity; 
        else if (item == "DT")
            rental += 20 * quantity; 
        else if (item == "LC")
            rental += 25 * quantity; 
        else if (item == "LV")
            rental += 20 * quantity; 
        else if (item == "BV")
            rental += 12 * quantity; 
    }

    totalrental = rental;

    cout << "------------------Receipt------------------" << endl;
    cout << setw(26) << left << "Name" << ":" <<name << endl;
    cout << setw(26) << "Contact number" << ":" <<contactnum << endl;
    cout << setw(26) << "Numbers of tickets" <<":" << ticketnum << endl;
    cout << setw(26) << "Number of child" << ":" <<child << endl;
    cout << setw(26) << "Number of adult " << ":" <<adult << endl;
    cout << setw(26) << "Number of senior citizen "<< ":" << senior << endl;
    cout << fixed << setprecision(2);
    cout << setw(26) << "Total admission fee" <<":RM " << fee << endl;
    cout << setw(26) << "Total rental fee" <<":RM " << totalrental << endl;
    cout << setw(26) << "Total Fee" << ":RM " <<totalrental+fee << endl;
    cout << setw(26) << "Tax"<< ":RM " <<(totalrental+fee)*0.06<< endl;
    cout << setw(26) << "Amount Due"<<":RM " << ((totalrental+fee)*0.06)+(totalrental+fee)<< endl;

    return 0;
}
