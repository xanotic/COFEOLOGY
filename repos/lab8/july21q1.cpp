#include <iostream>
using namespace std;

int main() {
    
    int age,car = 0;
    char typeofvehicle;

    while (age != -1)
    {
        cout << "enter your age (-1 to exit)" << endl;
        cin >> age;
        if (age == -1) {
            break;
        }
        cout << "enter your vehicle type vehicle ('B' for Bus / 'C' for Car / 'M' for Motorcycle)." << endl;
        cin >> typeofvehicle;
        if (typeofvehicle == 'C' || typeofvehicle == 'c')
            {
                car++;
            }

    }
    cout << "the total number of students who are come to school by car is " << car << endl;
    


    return 0;
}
