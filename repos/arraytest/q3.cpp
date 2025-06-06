#include <iostream>
#include <string>
#include <vector>

using namespace std;
//To insert temperature for 3 days using array and display the highest temperature.
int main(){

    double temperature[3], highest = -1000;
    for (int i = 0; i < sizeof(temperature)/sizeof(temperature[0]); i++)
    {
        cin >> temperature[i];
        if (temperature[i] > highest)
        {
            highest = temperature[i];
        }
        
    }
    cout << "highest temperature is " << highest << endl;
    
}