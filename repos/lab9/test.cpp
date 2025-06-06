#include <iostream>
using namespace std;

int hello(int& x){
    x = 5;
    return x+x;
}
int main() {
    int y = 10;
    cout << y;
    hello(y);
    cout << hello(y);
}