#include <iostream>
#include <iomanip>
#include <string>
using namespace std;


    double calculateTotalPayment(char roomCode, int nightsStayed) {
    const double roomRates[] = {155.0, 120.0, 100.0};
    const double gstRate = 0.06;
    
    switch (roomCode) {
        case 'D':
            return roomRates[0] * nightsStayed * (1 + gstRate);
        case 'S':
            return roomRates[1] * nightsStayed * (1 + gstRate);
        case 'E':
            return roomRates[2] * nightsStayed * (1 + gstRate);
        default:
            return -1.0; 
    }
}

int main() {
    char roomCode;
    int nightsStayed;

    while (true) {
        cout << "Enter the room's code (D/S/E) or 'stop' to quit: ";
        cin >> roomCode;

        if (roomCode == 's' || roomCode == 'S') {
            string stopInput;
            cout << "Are you sure you want to stop? (yes/no): ";
            cin >> stopInput;
            if (stopInput == "yes" || stopInput == "Yes") {
                break;
            }
            continue;
        }

        cout << "Enter the number of nights stayed: ";
        cin >> nightsStayed;

        double totalPayment = calculateTotalPayment(toupper(roomCode), nightsStayed);

        if (totalPayment >= 0) {
            cout << "Number of nights stayed: " << nightsStayed << endl;
            cout << "Total payment (including GST): RM " << fixed << setprecision(2) << totalPayment << endl;
        } else {
            cout << "Invalid room code. Please enter D, S, or E." << endl;
        }
    }

    return 0;
}
