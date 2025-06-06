#include <iostream>
#include <string>
#include <vector>

using namespace std;
//To insert temperature for 3 days using array and find the average.
int main(){

    double temperature[3], average, total;
    for (int i = 0; i < sizeof(temperature)/ sizeof(temperature[0]); i++)
    {
        cout << "insert your temperature : ";
        cin >> temperature[i];
        total += temperature[i];
    }

    average = total / 3;
    cout << average << endl;
    
    
    
}