#include <iostream>
using namespace std;

int main() {
    int a = 1;
    int number;
    int result = 0;

    cout << "Enter an integer number: ";
    cin >> number;
    
    while (a <= 12) {
        result = a * number;
        cout << a << " * " << number << " = " << result << endl;
        a++;
    }
    
    cout << "Thanks you ..." << endl;
    
    return 0;
}