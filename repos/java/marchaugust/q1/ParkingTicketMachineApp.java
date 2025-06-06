package q1;
public class ParkingTicketMachineApp {
    public static void main(String[] args ){
            //question c
    ParkingTicketMachine ptml = new ParkingTicketMachine (13, 45, 16, 20, 1.00);

    System.out.println("Hour entry is : " + ptml.getHourEntry() + "\n"+ "hour exist is : " + ptml.getHourExit());
    }
}
