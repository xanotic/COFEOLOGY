#include <iostream>
using namespace std;


void Value(double x, double &y, int i, int &j);
void main()
{
double x1 = 32.0;
double y1 = 21.0;
int z1 = 10;
int j = 20;
Value(x1,y1,z1,j);
cout << x1 <<" "<< y1 <<" "<<" "<< z1 <<" "<< j;
x1 = 1.5;
y1 = 2.3;
z1 = 8;
j = 9;
Value(y1,x1,j,z1);
cout<<endl;
cout << x1 <<" "<< y1 <<" "<<" "<< z1 <<" "<< j;
}
void Value(double x, double& y, int i, int& j)
{
x = y;
y = x;
i = 2 * i;
j = 2 * j;
}
    
 

