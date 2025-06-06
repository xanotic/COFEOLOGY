#include <iostream>
using namespace std;

int main() {
    int A = 80, B = 60, C = 50,  D = 40;    

    int markah;
    char grade;

    cout << "Masukkan markah BM kau: ";
    cin >> markah;

    if (markah >= A) {
        grade = 'A';
    } else if (markah >= B) {
        grade = 'B';
    } else if (markah >= C) {
        grade = 'C';
    } else if (markah >= D) {
        grade = 'D';
    } else {
        grade = 'F';
    }

    cout << "Ini grade kau: " << grade << endl;

    return 0;
}