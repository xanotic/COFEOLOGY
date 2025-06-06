#include <iostream>
using namespace std;

int main()
{
    char vehicleType;
    double hoursSpent, rate, parkingCharge;

    cout << "Enter the type of vehicle (c = car, b = bus, t = truck): ";
    cin >> vehicleType;
    cout << "Enter the number of hours spent: ";
    cin >> hoursSpent;

    if (vehicleType == 'c' || vehicleType == 'C')
    {
        rate = 2.00;
    }
    else if (vehicleType == 'b' || vehicleType == 'B')
    {
        rate = 3.00;
    }
    else if (vehicleType == 't' || vehicleType == 'T')
    {
        rate = 4.00;
    }
    else
    {
        cout << "Invalid vehicle type";
        return 1;
    }

    parkingCharge = hoursSpent * rate;
    cout << "Parking charge: RM" << parkingCharge << endl;

    return 0;
}
