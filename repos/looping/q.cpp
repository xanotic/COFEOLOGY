#include <iostream>
using namespace std;

int main() {
  int i = 0;
  while (i <= 30) 
  {
    if (i <= 10)
    {
        cout << "0" << i;
    }
    else
    {
        cout << " " << i;
    }
    if (i % 10 == 0)
    {
        cout << endl;
    }
    i++;
  }
  return 0;
}
