#include <iostream>
using namespace std;

int main() {
 
    int above2k = 0;
    int i = 1;

    while (i <= 10) {
      
        double salary;
        cout << "Enter salary " << i << ":";
        cin >> salary;
        ++i;

     
        if (salary > 2000) {
            above2k++;
        }
    }

    cout << "salaries greater than RM2000: " << above2k << endl;

    return 0;
}
