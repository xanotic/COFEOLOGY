package q1;
public class ParkingTicketMachine {

    private int hourEntry; //24 hours system for entry
    private int minEntry; //entry in minutes
    private int hourExit; //24 hours system for exit
    private int minExit; //exit in minutes
    private double hourlyParkingRate;

    //normal constructor
    public ParkingTicketMachine(int het, int met, int hex, int mex, double hpr){
        hourEntry = het;
        minEntry = met;
        hourExit = hex;
        minExit = mex;
        hourlyParkingRate = hpr;
    }

    //mutator 
    public void setHourEntry(int het){
        hourEntry = het;
    }

    public void setMinEntry(int met){
        minEntry = met;
    }

    public void setHourExit(int hex){
        hourExit = hex;
    }

    public void setMinExit(int mex){
        minExit = mex;
    }

    public void setHourlyParkingRate(double hpr){
        hourlyParkingRate = hpr;
    }

    //accessor
    public int getHourEntry(){
        return hourEntry;
    }
    public int getMinEntry(){
        return minEntry;
    }
    public int getHourExit(){
        return hourExit;
    }
    public int getMinExit(){
        return minExit;
    }
    public double getHourlyParkingRate(){
        return hourlyParkingRate;
    }

    public double calcDiffInHours(){
        if (minExit < minEntry) {
            minExit += 60;
            hourExit -= 1;
        }
        int hour = (hourExit - hourEntry) * 60; 
        int min = minExit - minEntry; 
        double diff = (hour + min) / 60.0; 
        return diff;         
    }
    
}   
