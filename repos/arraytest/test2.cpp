#include <iostream>
#include <string>
#include <vector>

using namespace std;
//To insert temperature for 3 days using array and find the average.
int main(){
    double temp[3], average, total;
    for (int i = 0; i < sizeof(temp) / sizeof(temp[0]); i++)
    {
        cout << "insert temperature : ";
        cin >> temp[i];
        total += temp[i];
    }
    average = total/3;
    cout << "average is " << average << endl;
    
}