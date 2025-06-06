#include <iostream>

using namespace std;

int main() {
    int sum_even = 0, sum_odd = 0, input;

    cout << "insert integer (if you want to end insert any alphabet)" << endl;

    while (cin >> input) {
        if (input % 2 == 0) {
            sum_even += input;
        } else {
            sum_odd += input;
        }
    }

    cout << "Sum of even integer: " << sum_even << endl;
    cout << "Sum of odd integer: " << sum_odd << endl;

    return 0;
}
