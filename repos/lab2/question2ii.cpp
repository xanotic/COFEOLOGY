#include <iostream>
using namespace std;
#include <cmath>
int main()
{

    float a, b, c, y;

    cout << "insert the value of a : ";
    cin >> a;
    cout << "insert the value of b : ";
    cin >> b;
    cout << "insert the value of c : ";
    cin >> c;

    y = sqrt(a * (b + c) * (b - c));

    cout << "heres the value : " << y;

    return 0;
}