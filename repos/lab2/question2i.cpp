using namespace std;
#include <iostream>

#include <cmath>

int main()
{

    float a, c, z;

    cout << "insert the variable of a in ([a^2 + 4ac] / 2) : ";
    cin >> a;
    cout << "insert the variable of c in ([a^2 + 4ac] / 2) : ";
    cin >> c;

    z = (pow(a, 2) + 4 * a * c) / 2;

    cout << "heres the value : " << z;

    return 0;
}
