#include <iostream>
using namespace std;

int main (){

    int i;
    int j = 1;
    cout << "insert number : ";
    cin >> i;
    system("cls");

    cout << "Number Squares" << endl;
    cout << "______ _______" << endl << endl;

    while (j <= i)
    {
        cout<<j<<" "<<j*j<<endl;
        j++;
    }   
}