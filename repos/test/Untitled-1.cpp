#include <iostream>
using namespace std;

int main () {

    string soalan = "kau dah makan lom ";
    string jawapan;

    cout << soalan;
    cin >> jawapan;

    for (char &c : jawapan) {
        c = tolower(c);

    if (jawapan == "yea") {
        cout << "weyh nice do kau ";
    } else if (jawapan == "belum") {
        cout << "ha awal la jom makan" ;
    } else {
        cout << "kau borak apa sia ";
    }
    
    return 0;

    }
}