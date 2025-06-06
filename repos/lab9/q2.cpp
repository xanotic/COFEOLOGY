#include <iostream>
using namespace std;

void mainMenu();
void Add(int, int);
void Sub(int, int);
void displayRes(int);

int main() {
    int x, y, choice;
    cout << "Please enter 2 numbers: ";
    cin >> x >> y;
    mainMenu();
    cout << "Please enter your choice: ";
    cin >> choice;
    if (choice == 1)
        Add(x, y);
    else if (choice == 2)
        Sub(x, y);
    else
        cout << "Wrong choice";
    return 0;
}

void mainMenu() {
    cout << "Mathematical operations : " << endl;
    cout << "1. Addition\n" << "2. Subtraction\n";
}

void Add(int a, int b) {
    int r;
    r = a + b;
    displayRes(r);
}

void Sub(int a, int b) {
    int r;
    r = a - b;
    displayRes(r);
}

void displayRes(int res) {
    cout << "The answer is " << res << endl;
}
