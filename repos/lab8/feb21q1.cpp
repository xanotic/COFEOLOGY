#include <iostream>
using namespace std;

int main() {
    int numCandidates = 300;
    int Short = 0, medium = 0, tall = 0;


    for (int i = 1; i <= numCandidates; ++i) {
        double height;

        cout << "Enter the height (in meters) for candidate " << i << ": ";
        cin >> height;

        if (height < 165.0) {
            Short++;
        } else if (height >= 165.0 && height <= 184.0) {
            medium++;
        } else {
            tall++;
        }
    }

    cout << "Height Categories:" << endl;
    cout << "Short (<165cm): " << Short << " candidates" << endl;
    cout << "Medium (165-184cm): " << medium << " candidates" << endl;
    cout << "Tall (>185cm): " << tall << " candidates" << endl;

    return 0;
}
