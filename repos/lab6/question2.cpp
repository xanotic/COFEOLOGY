#include <iostream>
using namespace std;

int main (){

    double num;

    cout << "enter a number > ";
    cin >> num;

    while (num > 0){
        cout << num << ", ";
        num--;
    }
    cout << "FIRE!";

    return 0;

}