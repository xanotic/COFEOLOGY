#include <iostream>
#include <string>
using namespace std;

int main() {
    int moviecode, numberofticket;
    double newprice;
    string paymentcode, movieTitle;

    // Input moviecode, numberofticket, paymentcode
    cout << "Enter movie code (1 for Frozen, other for Spiderman): ";
    cin >> moviecode;
    cout << "Enter number of tickets: ";
    cin >> numberofticket;
    cout << "Enter payment code (type 'Credit Card' for credit card payment): ";
    cin.ignore(); // To clear the newline character from the buffer
    getline(cin, paymentcode);

    // Check moviecode
    if (moviecode == 1) {
        newprice = numberofticket * 20.0; // RM20 per ticket
        movieTitle = "Frozen";
    } else {
        newprice = numberofticket * 25.0; // RM25 per ticket
        movieTitle = "Spiderman";
    }

    // Check payment code
    if (paymentcode == "Credit Card") {
        newprice = newprice * 1.10; // Increase by 10%
    }

    // Display movie title and new price
    cout << "Movie Title12: " << movieTitle << endl;
    cout << "Total Price: RM" << newprice << endl;

    return 0;
}
