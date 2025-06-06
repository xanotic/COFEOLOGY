#include <iostream>
using namespace std;

int main() {
    
    int customer = 0;
    double cost,bagPrice = 8.50;

    cout << "Enter the number of customers: ";
    cin >> customer;

    for (int i = 0; i < customer; i++) {
        int beans;
        double discount = 0.0;

        cout << "Enter the number of bags for customer " << i + 1 << ": ";
        cin >> beans;

        if (beans >= 20) {
            discount = 0.10;
        } else if (beans >= 10) {
            discount = 0.05;
        } 
        cost = beans * bagPrice * (1-discount);

        cout << "Total cost for customer " << i + 1 << ": RM" << cost << endl;
    }

    return 0;
}
